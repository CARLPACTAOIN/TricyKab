<?php

namespace App\Services;

use App\Jobs\InitiateDispatchJob;
use App\Models\Booking;
use App\Models\BookingDispatchAttempt;
use App\Models\BookingDispatchCandidate;
use App\Models\User;
use App\Support\Geo;
use Illuminate\Support\Facades\DB;

class PassengerBookingService
{
    public function __construct(
        private readonly FareCalculatorService $fareCalculator,
        private readonly TripService $tripService,
        private readonly AuditLogger $audit,
    ) {}

    /**
     * @param  array{ride_type: string, pickup: array, destination: array}  $validated
     */
    public function create(User $passenger, array $validated): Booking
    {
        $rideTypeDb = strtolower($validated['ride_type']);

        $pickupLat = (float) $validated['pickup']['latitude'];
        $pickupLng = (float) $validated['pickup']['longitude'];
        $destLat = (float) $validated['destination']['latitude'];
        $destLng = (float) $validated['destination']['longitude'];

        $distanceKm = Geo::haversineKm($pickupLat, $pickupLng, $destLat, $destLng);

        if ($rideTypeDb === 'special') {
            $fareBreakdown = $this->fareCalculator->calculateSpecial($distanceKm);
            $fareAmount = $fareBreakdown['suggested_fare'];
        } else {
            $fareBreakdown = $this->fareCalculator->calculate($distanceKm);
            $fareAmount = $fareBreakdown['total_fare'];
        }

        $booking = DB::transaction(function () use (
            $passenger,
            $rideTypeDb,
            $pickupLat,
            $pickupLng,
            $destLat,
            $destLng,
            $validated,
            $distanceKm,
            $fareAmount
        ) {
            return Booking::query()->create([
                'passenger_id' => $passenger->id,
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'pickup_address' => $validated['pickup']['address'],
                'pickup_notes' => $validated['pickup']['notes'] ?? null,
                'destination_lat' => $destLat,
                'destination_lng' => $destLng,
                'destination_address' => $validated['destination']['address'],
                'ride_type' => $rideTypeDb,
                'status' => Booking::STATUS_SEARCHING_DRIVER,
                'fare_amount' => $fareAmount,
                'distance_km' => round($distanceKm, 2),
            ]);
        });

        InitiateDispatchJob::dispatch($booking->id);

        return $booking->fresh();
    }

    /**
     * PRD-aligned booking payload for JSON API (create + read).
     *
     * @return array<string, mixed>
     */
    public function bookingToApiData(Booking $booking): array
    {
        $distanceKm = (float) ($booking->distance_km ?? 0);
        $estimatedMeters = (int) round($distanceKm * 1000);

        $data = [
            'id' => $booking->id,
            'reference' => $booking->booking_reference,
            'status' => $booking->status,
            'ride_type' => strtoupper((string) $booking->ride_type),
            'estimated_distance_meters' => $estimatedMeters,
            'estimated_duration_seconds' => $this->estimatedDurationSeconds($distanceKm),
            'estimated_fare' => $booking->fare_amount !== null
                ? number_format((float) $booking->fare_amount, 2, '.', '')
                : null,
            'search_radius_meters' => $this->searchRadiusMeters(),
            'pickup' => [
                'latitude' => $booking->pickup_lat !== null ? (float) $booking->pickup_lat : null,
                'longitude' => $booking->pickup_lng !== null ? (float) $booking->pickup_lng : null,
                'address' => $booking->pickup_address,
                'notes' => $booking->pickup_notes,
            ],
            'destination' => [
                'latitude' => $booking->destination_lat !== null ? (float) $booking->destination_lat : null,
                'longitude' => $booking->destination_lng !== null ? (float) $booking->destination_lng : null,
                'address' => $booking->destination_address,
            ],
            'driver_id' => $booking->driver_id,
            'created_at' => $booking->created_at?->utc()->toIso8601String(),
            'accepted_at' => $booking->accepted_at?->utc()->toIso8601String(),
            'cancelled_at' => $booking->cancelled_at?->utc()->toIso8601String(),
        ];

        return $data;
    }

    /**
     * Booking JSON for driver-scoped reads (assignments, dispatch offer cards).
     *
     * @return array<string, mixed>
     */
    public function bookingToDriverApiData(Booking $booking): array
    {
        $data = $this->bookingToApiData($booking);
        $passenger = $booking->relationLoaded('passenger') ? $booking->passenger : $booking->passenger()->first();
        $name = $passenger?->name ?? 'Passenger';
        $data['passenger'] = [
            'id' => $passenger?->id,
            'display_name' => $name,
            'initials' => $this->initialsFromDisplayName($name),
        ];

        return $data;
    }

    public function initialsFromDisplayName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/', $name) ?: [];
        if (count($parts) === 1) {
            return strtoupper(substr($parts[0], 0, 2));
        }

        return strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1));
    }

    /**
     * Passenger-initiated cancel per PRD §6.3 / §9.2.
     *
     * @return array{booking: Booking}|array{error: string, message: string, http: int}
     */
    public function cancelByPassenger(
        User $passenger,
        Booking $booking,
        string $reasonCode,
        ?string $notes
    ): array {
        if ($booking->passenger_id !== $passenger->id) {
            return [
                'error' => 'FORBIDDEN',
                'message' => 'This booking does not belong to the authenticated passenger.',
                'http' => 403,
            ];
        }

        if ($booking->status === Booking::STATUS_CANCELLED_BY_PASSENGER) {
            return ['booking' => $booking->fresh() ?? $booking];
        }

        if ($booking->isCancelled() || ! $booking->isActive()) {
            return [
                'error' => 'BOOKING_NOT_CANCELLABLE',
                'message' => 'Booking cannot be cancelled in its current state.',
                'http' => 409,
            ];
        }

        if (! $this->passengerMayCancelByPrdRules($booking)) {
            return [
                'error' => 'BOOKING_NOT_CANCELLABLE',
                'message' => 'Cancellation is not allowed after the grace period following driver assignment.',
                'http' => 422,
            ];
        }

        $payload = json_encode([
            'reason_code' => $reasonCode,
            'notes' => $notes,
        ], JSON_THROW_ON_ERROR);

        return DB::transaction(function () use ($booking, $payload) {
            /** @var Booking|null $locked */
            $locked = Booking::query()->lockForUpdate()->find($booking->id);
            if ($locked === null) {
                return [
                    'error' => 'NOT_FOUND',
                    'message' => 'Booking not found.',
                    'http' => 404,
                ];
            }

            if ($locked->status === Booking::STATUS_CANCELLED_BY_PASSENGER) {
                return ['booking' => $locked->fresh() ?? $locked];
            }

            if ($locked->isCancelled() || ! $locked->isActive()) {
                return [
                    'error' => 'BOOKING_NOT_CANCELLABLE',
                    'message' => 'Booking cannot be cancelled in its current state.',
                    'http' => 409,
                ];
            }

            if (! $this->passengerMayCancelByPrdRules($locked)) {
                return [
                    'error' => 'BOOKING_NOT_CANCELLABLE',
                    'message' => 'Cancellation is not allowed after the grace period following driver assignment.',
                    'http' => 422,
                ];
            }

            $openAttemptIds = BookingDispatchAttempt::query()
                ->where('booking_id', $locked->id)
                ->where('status', BookingDispatchAttempt::STATUS_OPEN)
                ->pluck('id');

            if ($openAttemptIds->isNotEmpty()) {
                BookingDispatchCandidate::query()
                    ->whereIn('dispatch_attempt_id', $openAttemptIds)
                    ->where('response_status', BookingDispatchCandidate::RESPONSE_PENDING)
                    ->update([
                        'response_status' => BookingDispatchCandidate::RESPONSE_EXPIRED,
                        'responded_at' => now(),
                    ]);

                BookingDispatchAttempt::query()
                    ->whereIn('id', $openAttemptIds)
                    ->update(['status' => BookingDispatchAttempt::STATUS_CANCELLED]);
            }

            $previousStatus = $locked->status;
            $this->tripService->abortTripIfExists($locked);

            $locked->status = Booking::STATUS_CANCELLED_BY_PASSENGER;
            $locked->cancelled_at = now();
            $locked->cancellation_reason = $payload;
            $locked->save();

            $this->audit->log(
                actor: $locked->passenger,
                objectType: 'BOOKING',
                objectId: $locked->id,
                action: 'BOOKING_CANCELLED_BY_PASSENGER',
                previous: ['status' => $previousStatus],
                next: ['status' => $locked->status],
            );

            return ['booking' => $locked->fresh()];
        });
    }

    public function passengerMayCancelByPrdRules(Booking $booking): bool
    {
        if (in_array($booking->status, [
            Booking::STATUS_CREATED,
            Booking::STATUS_SEARCHING_DRIVER,
        ], true)) {
            return true;
        }

        if (in_array($booking->status, [
            Booking::STATUS_DRIVER_ASSIGNED,
            Booking::STATUS_DRIVER_ON_THE_WAY,
        ], true)) {
            if ($booking->accepted_at === null) {
                return false;
            }

            return now()->lte($booking->accepted_at->copy()->addMinute());
        }

        return false;
    }

    public function estimatedDurationSeconds(float $distanceKm): int
    {
        $speed = max((float) config('booking.average_speed_kmh', 25), 1.0);

        return (int) round(($distanceKm / $speed) * 3600);
    }

    public function searchRadiusMeters(): int
    {
        return (int) config('booking.search_radius_meters', 1000);
    }
}
