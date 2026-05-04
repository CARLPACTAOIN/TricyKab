<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingDispatchAttempt;
use App\Models\BookingDispatchCandidate;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DriverDispatchService
{
    public function __construct(
        private readonly PassengerBookingService $passengerBookings,
        private readonly TripService $trips,
        private readonly AuditLogger $audit,
    ) {}

    private const DRIVER_CANCEL_REASON_NO_SHOW = 'PASSENGER_NO_SHOW';

    private const DRIVER_CANCEL_ALLOWED_STATES = [
        Booking::STATUS_DRIVER_ASSIGNED,
        Booking::STATUS_DRIVER_ON_THE_WAY,
        Booking::STATUS_DRIVER_ARRIVED,
    ];

    /**
     * Pending offers for this driver (open attempt, not expired, booking still searching).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function listPendingOffers(Driver $driver): Collection
    {
        return BookingDispatchCandidate::query()
            ->with(['dispatchAttempt.booking.passenger'])
            ->where('driver_id', $driver->id)
            ->where('response_status', BookingDispatchCandidate::RESPONSE_PENDING)
            ->whereHas('dispatchAttempt', function ($q): void {
                $q->where('status', BookingDispatchAttempt::STATUS_OPEN)
                    ->where('broadcast_expires_at', '>=', now());
            })
            ->whereHas('dispatchAttempt.booking', function ($q): void {
                $q->where('status', Booking::STATUS_SEARCHING_DRIVER);
            })
            ->orderBy('id')
            ->get()
            ->map(fn (BookingDispatchCandidate $c) => $this->candidateToOfferPayload($c));
    }

    /**
     * @return array<string, mixed>
     */
    public function candidateToOfferPayload(BookingDispatchCandidate $candidate): array
    {
        $attempt = $candidate->dispatchAttempt;
        $booking = $attempt->booking;
        $expiresAt = $attempt->broadcast_expires_at;
        $countdownSeconds = 0;
        if ($expiresAt !== null) {
            $countdownSeconds = max(0, $expiresAt->getTimestamp() - now()->getTimestamp());
        }

        $distanceKm = (float) ($booking->distance_km ?? 0);
        $durationSec = $this->passengerBookings->estimatedDurationSeconds($distanceKm);
        $pickupDistanceMeters = (int) $candidate->distance_meters;

        $bookingData = $this->passengerBookings->bookingToDriverApiData($booking);

        return [
            'candidate_id' => $candidate->id,
            'dispatch_attempt_id' => $attempt->id,
            'attempt_no' => $attempt->attempt_no,
            'expires_at' => $expiresAt?->utc()->toIso8601String(),
            'countdown_seconds' => $countdownSeconds,
            'distance_meters' => $pickupDistanceMeters,
            'rank_order' => $candidate->rank_order,
            'booking' => $bookingData,
            'display' => [
                'pickup_distance' => $pickupDistanceMeters > 0
                    ? $this->formatDistanceMetersLabel($pickupDistanceMeters)
                    : null,
                'estimated_distance' => $this->formatRouteDistanceLabel($distanceKm),
                'estimated_duration' => $this->formatDurationLabel($durationSec),
            ],
        ];
    }

    private function formatDistanceMetersLabel(int $meters): string
    {
        if ($meters >= 1000) {
            return number_format($meters / 1000, 1, '.', '').' km';
        }

        return $meters.' m';
    }

    private function formatRouteDistanceLabel(float $distanceKm): string
    {
        if ($distanceKm < 0.05) {
            return '0 km';
        }

        return number_format($distanceKm, 1, '.', '').' km';
    }

    private function formatDurationLabel(int $durationSeconds): string
    {
        if ($durationSeconds < 60) {
            return $durationSeconds.' sec';
        }
        $minutes = (int) round($durationSeconds / 60);
        if ($minutes < 60) {
            return $minutes.' min';
        }
        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        return $rest > 0 ? "{$hours} h {$rest} min" : "{$hours} h";
    }

    /**
     * @return array{booking: Booking}|array{error: string, message: string, http: int, details?: array<string, mixed>}
     */
    public function acceptOffer(
        User $user,
        Driver $driver,
        Booking $booking,
        int $dispatchAttemptId,
        int $candidateId,
    ): array {
        if ($driver->user_id !== $user->id) {
            return [
                'error' => 'FORBIDDEN',
                'message' => 'Driver profile does not match authenticated user.',
                'http' => 403,
            ];
        }

        return DB::transaction(function () use ($driver, $booking, $dispatchAttemptId, $candidateId) {
            /** @var Booking $bookingLocked */
            $bookingLocked = Booking::query()->lockForUpdate()->find($booking->id);
            if ($bookingLocked === null) {
                return [
                    'error' => 'NOT_FOUND',
                    'message' => 'Booking not found.',
                    'http' => 404,
                ];
            }

            if ($bookingLocked->status !== Booking::STATUS_SEARCHING_DRIVER) {
                if (
                    $bookingLocked->status === Booking::STATUS_DRIVER_ASSIGNED
                    && (int) $bookingLocked->driver_id === (int) $driver->id
                ) {
                    $this->trips->createPreStartTrip($bookingLocked, $driver);

                    return ['booking' => $bookingLocked->fresh()];
                }

                return [
                    'error' => 'DISPATCH_RACE_LOST',
                    'message' => 'Another driver was assigned first.',
                    'http' => 409,
                    'details' => [
                        'booking_id' => $bookingLocked->id,
                        'assigned_driver_id' => $bookingLocked->driver_id,
                    ],
                ];
            }

            $candidate = BookingDispatchCandidate::query()
                ->lockForUpdate()
                ->whereKey($candidateId)
                ->where('driver_id', $driver->id)
                ->where('response_status', BookingDispatchCandidate::RESPONSE_PENDING)
                ->first();

            if ($candidate === null) {
                return [
                    'error' => 'INVALID_OFFER',
                    'message' => 'Offer not found or no longer pending.',
                    'http' => 422,
                ];
            }

            $attempt = BookingDispatchAttempt::query()
                ->lockForUpdate()
                ->find($dispatchAttemptId);

            if (
                $attempt === null
                || (int) $attempt->booking_id !== (int) $bookingLocked->id
                || (int) $candidate->dispatch_attempt_id !== (int) $attempt->id
                || $attempt->status !== BookingDispatchAttempt::STATUS_OPEN
            ) {
                return [
                    'error' => 'INVALID_OFFER',
                    'message' => 'Dispatch attempt is not open for this booking.',
                    'http' => 422,
                ];
            }

            if ($attempt->broadcast_expires_at && now()->gt($attempt->broadcast_expires_at)) {
                return [
                    'error' => 'OFFER_EXPIRED',
                    'message' => 'This dispatch offer has expired.',
                    'http' => 422,
                ];
            }

            $previousStatus = $bookingLocked->status;
            $bookingLocked->driver_id = $driver->id;
            $bookingLocked->tricycle_id = $driver->tricycle_id;
            $bookingLocked->status = Booking::STATUS_DRIVER_ASSIGNED;
            $bookingLocked->accepted_at = now();
            $bookingLocked->save();

            $this->trips->createPreStartTrip($bookingLocked, $driver);

            $this->audit->log(
                actor: $driver->user,
                objectType: 'BOOKING',
                objectId: $bookingLocked->id,
                action: 'BOOKING_DRIVER_ASSIGNED',
                previous: ['status' => $previousStatus],
                next: ['status' => $bookingLocked->status, 'driver_id' => $driver->id],
            );

            $attempt->winner_driver_id = $driver->id;
            $attempt->status = BookingDispatchAttempt::STATUS_ASSIGNED;
            $attempt->save();

            $candidate->response_status = BookingDispatchCandidate::RESPONSE_ACCEPTED;
            $candidate->responded_at = now();
            $candidate->save();

            BookingDispatchCandidate::query()
                ->where('dispatch_attempt_id', $attempt->id)
                ->where('id', '<>', $candidate->id)
                ->where('response_status', BookingDispatchCandidate::RESPONSE_PENDING)
                ->update([
                    'response_status' => BookingDispatchCandidate::RESPONSE_LOST_RACE,
                    'responded_at' => now(),
                ]);

            BookingDispatchAttempt::query()
                ->where('booking_id', $bookingLocked->id)
                ->where('id', '<>', $attempt->id)
                ->where('status', BookingDispatchAttempt::STATUS_OPEN)
                ->update(['status' => BookingDispatchAttempt::STATUS_CANCELLED]);

            return ['booking' => $bookingLocked->fresh()];
        });
    }

    /**
     * @return array{status: string}|array{error: string, message: string, http: int}
     */
    public function declineOffer(
        User $user,
        Driver $driver,
        Booking $booking,
        int $dispatchAttemptId,
        int $candidateId,
        string $reasonCode,
    ): array {
        if ($driver->user_id !== $user->id) {
            return [
                'error' => 'FORBIDDEN',
                'message' => 'Driver profile does not match authenticated user.',
                'http' => 403,
            ];
        }

        return DB::transaction(function () use ($driver, $booking, $dispatchAttemptId, $candidateId, $reasonCode) {
            $candidate = BookingDispatchCandidate::query()
                ->lockForUpdate()
                ->whereKey($candidateId)
                ->where('driver_id', $driver->id)
                ->where('response_status', BookingDispatchCandidate::RESPONSE_PENDING)
                ->first();

            if ($candidate === null) {
                return [
                    'error' => 'INVALID_OFFER',
                    'message' => 'Offer not found or no longer pending.',
                    'http' => 422,
                ];
            }

            $attempt = BookingDispatchAttempt::query()
                ->lockForUpdate()
                ->find($dispatchAttemptId);

            if (
                $attempt === null
                || (int) $attempt->booking_id !== (int) $booking->id
                || (int) $candidate->dispatch_attempt_id !== (int) $attempt->id
                || $attempt->status !== BookingDispatchAttempt::STATUS_OPEN
            ) {
                return [
                    'error' => 'INVALID_OFFER',
                    'message' => 'Dispatch attempt is not open for this booking.',
                    'http' => 422,
                ];
            }

            if ($booking->status !== Booking::STATUS_SEARCHING_DRIVER) {
                return [
                    'error' => 'INVALID_OFFER',
                    'message' => 'Booking is no longer accepting dispatch responses.',
                    'http' => 422,
                ];
            }

            if ($attempt->broadcast_expires_at && now()->gt($attempt->broadcast_expires_at)) {
                return [
                    'error' => 'OFFER_EXPIRED',
                    'message' => 'This dispatch offer has expired.',
                    'http' => 422,
                ];
            }

            $candidate->response_status = BookingDispatchCandidate::RESPONSE_DECLINED;
            $candidate->responded_at = now();
            $candidate->decline_reason_code = $reasonCode;
            $candidate->save();

            $this->audit->log(
                actor: $driver->user,
                objectType: 'BOOKING',
                objectId: $booking->id,
                action: 'BOOKING_OFFER_DECLINED',
                previous: ['response_status' => BookingDispatchCandidate::RESPONSE_PENDING],
                next: ['response_status' => $candidate->response_status, 'driver_id' => $driver->id],
                reason: $reasonCode,
            );

            return ['status' => 'DECLINED'];
        });
    }

    /**
     * Driver-initiated cancellation per PRD §6.3 / §11.
     *
     * @return array{booking: Booking}|array{error: string, message: string, http: int}
     */
    public function cancelByDriver(
        User $user,
        Driver $driver,
        Booking $booking,
        string $reasonCode,
        ?string $notes,
    ): array {
        if ($driver->user_id !== $user->id) {
            return [
                'error' => 'FORBIDDEN',
                'message' => 'Driver profile does not match authenticated user.',
                'http' => 403,
            ];
        }

        return DB::transaction(function () use ($user, $driver, $booking, $reasonCode, $notes) {
            /** @var Booking|null $locked */
            $locked = Booking::query()->lockForUpdate()->find($booking->id);
            if ($locked === null) {
                return ['error' => 'NOT_FOUND', 'message' => 'Booking not found.', 'http' => 404];
            }

            if ((int) $locked->driver_id !== (int) $driver->id) {
                return [
                    'error' => 'FORBIDDEN',
                    'message' => 'Booking is not assigned to this driver.',
                    'http' => 403,
                ];
            }

            if (! in_array($locked->status, self::DRIVER_CANCEL_ALLOWED_STATES, true)) {
                return [
                    'error' => 'BOOKING_NOT_CANCELLABLE',
                    'message' => 'Booking cannot be cancelled in its current state.',
                    'http' => 409,
                ];
            }

            $previousStatus = $locked->status;
            $isNoShow = strtoupper($reasonCode) === self::DRIVER_CANCEL_REASON_NO_SHOW;
            $newStatus = $isNoShow
                ? Booking::STATUS_NO_SHOW_PASSENGER
                : Booking::STATUS_CANCELLED_BY_DRIVER;

            $this->trips->abortTripIfExists($locked);

            $payload = json_encode([
                'reason_code' => $reasonCode,
                'notes' => $notes,
                'cancelled_by' => 'DRIVER',
                'driver_id' => $driver->id,
            ], JSON_THROW_ON_ERROR);

            $locked->status = $newStatus;
            $locked->cancelled_at = now();
            $locked->cancellation_reason = $payload;
            $locked->save();

            $this->audit->log(
                actor: $user,
                objectType: 'BOOKING',
                objectId: $locked->id,
                action: $isNoShow ? 'BOOKING_NO_SHOW_PASSENGER' : 'BOOKING_CANCELLED_BY_DRIVER',
                previous: ['status' => $previousStatus],
                next: ['status' => $newStatus],
                reason: $reasonCode,
            );

            return ['booking' => $locked->fresh()];
        });
    }
}
