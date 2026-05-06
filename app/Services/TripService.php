<?php

namespace App\Services;

use App\Jobs\FirebaseMirrorJob;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripLocationLog;
use App\Models\TripPassengerEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TripService
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    public function createPreStartTrip(Booking $booking, Driver $driver): Trip
    {
        if ($booking->passenger_id === null) {
            throw new \InvalidArgumentException('Booking has no passenger.');
        }

        $existing = Trip::query()->where('booking_id', $booking->id)->first();
        if ($existing !== null) {
            return $existing;
        }

        $trip = Trip::query()->create([
            'booking_id' => $booking->id,
            'driver_id' => $driver->id,
            'passenger_id' => $booking->passenger_id,
            'trip_status' => Trip::STATUS_PRE_START,
            'passenger_count' => 1,
            'gps_quality_status' => 'NORMAL',
        ]);

        $this->scheduleFirebaseProjection($trip->id);

        $this->audit->log(
            actor: $driver->user,
            objectType: 'TRIP',
            objectId: $trip->id,
            action: 'TRIP_PRE_START_CREATED',
            next: ['booking_id' => $booking->id, 'driver_id' => $driver->id],
        );

        return $trip;
    }

    protected function scheduleFirebaseProjection(int $tripId): void
    {
        DB::afterCommit(function () use ($tripId) {
            FirebaseMirrorJob::dispatch($tripId);
        });
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy_meters?: float|null}  $location
     * @return array{trip: Trip}|array{error: string, message: string, http: int}
     */
    public function recordArrival(User $user, Driver $driver, Trip $trip, array $location): array
    {
        if ((int) $trip->driver_id !== (int) $driver->id || $driver->user_id !== $user->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Trip does not belong to this driver.', 'http' => 403];
        }

        return DB::transaction(function () use ($trip, $driver, $location) {
            $trip = Trip::query()->lockForUpdate()->findOrFail($trip->id);
            $booking = Booking::query()->lockForUpdate()->findOrFail($trip->booking_id);

            if ($trip->trip_status !== Trip::STATUS_PRE_START) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip is not in PRE_START state.',
                    'http' => 422,
                ];
            }

            // Passenger may have confirmed pickup first (`passenger_ack_pickup_at`) which already
            // sets DRIVER_ARRIVED. Treat driver "arrive" as idempotent: record GPS only.
            if ($booking->status === Booking::STATUS_DRIVER_ARRIVED) {
                $this->appendLocation($trip, $driver, $location);
                $this->scheduleFirebaseProjection($trip->id);

                return ['trip' => $trip->fresh()];
            }

            if (! in_array($booking->status, [Booking::STATUS_DRIVER_ASSIGNED, Booking::STATUS_DRIVER_ON_THE_WAY], true)) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Booking is not awaiting pickup arrival.',
                    'http' => 422,
                ];
            }

            $this->appendLocation($trip, $driver, $location);

            $previousBookingStatus = $booking->status;
            $booking->status = Booking::STATUS_DRIVER_ARRIVED;
            $booking->save();

            $this->scheduleFirebaseProjection($trip->id);

            $this->audit->log(
                actor: $driver->user,
                objectType: 'BOOKING',
                objectId: $booking->id,
                action: 'BOOKING_DRIVER_ARRIVED',
                previous: ['status' => $previousBookingStatus],
                next: ['status' => $booking->status, 'trip_id' => $trip->id],
            );

            return ['trip' => $trip->fresh()];
        });
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy_meters?: float|null, started_at_client?: string|null}  $payload
     * @return array{trip: Trip}|array{error: string, message: string, http: int}
     */
    public function startTrip(User $user, Driver $driver, Trip $trip, array $payload): array
    {
        if ((int) $trip->driver_id !== (int) $driver->id || $driver->user_id !== $user->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Trip does not belong to this driver.', 'http' => 403];
        }

        return DB::transaction(function () use ($trip, $driver, $payload) {
            $trip = Trip::query()->lockForUpdate()->findOrFail($trip->id);
            $booking = Booking::query()->lockForUpdate()->findOrFail($trip->booking_id);

            if ($booking->status !== Booking::STATUS_DRIVER_ARRIVED) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Driver must arrive at pickup before starting the trip.',
                    'http' => 422,
                ];
            }

            if ($trip->trip_status !== Trip::STATUS_PRE_START) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip already started or finished.',
                    'http' => 422,
                ];
            }

            $now = now();
            $previousBookingStatus = $booking->status;
            $trip->trip_status = Trip::STATUS_IN_PROGRESS;
            $trip->started_at = $now;
            $trip->start_latitude = $payload['latitude'];
            $trip->start_longitude = $payload['longitude'];
            $trip->save();

            $booking->status = Booking::STATUS_TRIP_IN_PROGRESS;
            $booking->started_at = $now;
            $booking->save();

            $this->appendLocation($trip, $driver, [
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
                'accuracy_meters' => $payload['accuracy_meters'] ?? null,
            ]);

            $this->scheduleFirebaseProjection($trip->id);

            $this->audit->log(
                actor: $driver->user,
                objectType: 'TRIP',
                objectId: $trip->id,
                action: 'TRIP_STARTED',
                previous: ['booking_status' => $previousBookingStatus, 'trip_status' => Trip::STATUS_PRE_START],
                next: ['booking_status' => $booking->status, 'trip_status' => $trip->trip_status],
            );

            return ['trip' => $trip->fresh()];
        });
    }

    /**
     * @param  array{quantity: int, notes?: string|null}  $data
     * @return array{trip: Trip}|array{error: string, message: string, http: int}
     */
    public function addPassengers(User $user, Driver $driver, Trip $trip, array $data): array
    {
        if ((int) $trip->driver_id !== (int) $driver->id || $driver->user_id !== $user->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Trip does not belong to this driver.', 'http' => 403];
        }

        $qty = (int) $data['quantity'];
        if ($qty < 1) {
            return ['error' => 'VALIDATION_ERROR', 'message' => 'Quantity must be at least 1.', 'http' => 422];
        }

        return DB::transaction(function () use ($trip, $driver, $data, $qty) {
            $trip = Trip::query()->lockForUpdate()->findOrFail($trip->id);

            if ($trip->trip_status !== Trip::STATUS_IN_PROGRESS) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip must be in progress to add passengers.',
                    'http' => 422,
                ];
            }

            // PRD §9.5 / §12 — enforce tricycle capacity before incrementing passengers.
            $booking = Booking::query()->find($trip->booking_id);
            $tricycle = $booking?->tricycle()->first();
            $capacity = $tricycle !== null ? (int) ($tricycle->capacity ?? 0) : 0;
            $previousCount = (int) $trip->passenger_count;
            $proposedCount = $previousCount + $qty;

            if ($capacity > 0 && $proposedCount > $capacity) {
                return [
                    'error' => 'DRIVER_CAPACITY_EXCEEDED',
                    'message' => 'Adding these passengers would exceed the tricycle capacity.',
                    'http' => 409,
                ];
            }

            TripPassengerEvent::query()->create([
                'trip_id' => $trip->id,
                'event_type' => TripPassengerEvent::TYPE_ADDITIONAL_PASSENGER_ADDED,
                'quantity' => $qty,
                'notes' => $data['notes'] ?? null,
                'recorded_by_driver_id' => $driver->id,
            ]);

            $trip->passenger_count = $proposedCount;
            $trip->save();

            $this->scheduleFirebaseProjection($trip->id);

            $this->audit->log(
                actor: $driver->user,
                objectType: 'TRIP',
                objectId: $trip->id,
                action: 'TRIP_PASSENGERS_ADDED',
                previous: ['passenger_count' => $previousCount],
                next: ['passenger_count' => $proposedCount, 'added' => $qty, 'capacity' => $capacity],
                reason: $data['notes'] ?? null,
            );

            return ['trip' => $trip->fresh()];
        });
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy_meters?: float|null, ended_at_client?: string|null, manual_reason?: string|null}  $payload
     * @return array{trip: Trip}|array{error: string, message: string, http: int}
     */
    public function endTrip(User $user, Driver $driver, Trip $trip, array $payload): array
    {
        if ((int) $trip->driver_id !== (int) $driver->id || $driver->user_id !== $user->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Trip does not belong to this driver.', 'http' => 403];
        }

        return DB::transaction(function () use ($trip, $driver, $payload) {
            $trip = Trip::query()->lockForUpdate()->findOrFail($trip->id);
            $booking = Booking::query()->lockForUpdate()->findOrFail($trip->booking_id);

            if ($booking->status !== Booking::STATUS_TRIP_IN_PROGRESS) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip is not in progress.',
                    'http' => 422,
                ];
            }

            if ($trip->trip_status !== Trip::STATUS_IN_PROGRESS) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip is not active.',
                    'http' => 422,
                ];
            }

            $now = now();
            $previousBookingStatus = $booking->status;
            $trip->trip_status = Trip::STATUS_COMPLETED;
            $trip->ended_at = $now;
            $trip->end_latitude = $payload['latitude'];
            $trip->end_longitude = $payload['longitude'];
            $trip->end_method = ($payload['manual_reason'] ?? null) !== null ? 'MANUAL_DRIVER' : 'AUTO';

            if ($trip->started_at !== null) {
                $trip->computed_duration_seconds = max(0, $now->diffInSeconds($trip->started_at));
            }

            $trip->save();

            $booking->status = Booking::STATUS_COMPLETED;
            $booking->completed_at = $now;
            $booking->save();

            $this->appendLocation($trip, $driver, [
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
                'accuracy_meters' => $payload['accuracy_meters'] ?? null,
            ]);

            $this->scheduleFirebaseProjection($trip->id);

            $this->audit->log(
                actor: $driver->user,
                objectType: 'TRIP',
                objectId: $trip->id,
                action: 'TRIP_ENDED',
                previous: ['booking_status' => $previousBookingStatus, 'trip_status' => Trip::STATUS_IN_PROGRESS],
                next: [
                    'booking_status' => $booking->status,
                    'trip_status' => $trip->trip_status,
                    'end_method' => $trip->end_method,
                    'duration_seconds' => $trip->computed_duration_seconds,
                ],
                reason: $payload['manual_reason'] ?? null,
            );

            return ['trip' => $trip->fresh()];
        });
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy_meters?: float|null}  $location
     */
    public function appendDriverLocation(Driver $driver, Trip $trip, array $location): TripLocationLog
    {
        $log = $this->appendLocation($trip, $driver, $location);
        $this->scheduleFirebaseProjection($trip->id);

        return $log;
    }

    /**
     * @param  array{latitude: float, longitude: float, accuracy_meters?: float|null}  $location
     */
    protected function appendLocation(Trip $trip, Driver $driver, array $location): TripLocationLog
    {
        return TripLocationLog::query()->create([
            'trip_id' => $trip->id,
            'driver_id' => $driver->id,
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'accuracy_meters' => $location['accuracy_meters'] ?? null,
            'captured_at' => now(),
            'source' => 'DRIVER_APP',
        ]);
    }

    /**
     * Passenger confirms they are at the pickup point — mirrors driver “arrived” for booking status
     * when the trip has not yet started, so either party can satisfy the pickup-ready milestone.
     *
     * @return array{booking: Booking}|array{error: string, message: string, http: int}
     */
    public function passengerAckPickup(User $passengerUser, Booking $booking): array
    {
        if ((int) $booking->passenger_id !== (int) $passengerUser->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Booking does not belong to this passenger.', 'http' => 403];
        }

        return DB::transaction(function () use ($passengerUser, $booking) {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $trip = Trip::query()->lockForUpdate()->where('booking_id', $booking->id)->first();

            if ($trip === null) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'No trip exists for this booking.',
                    'http' => 422,
                ];
            }

            if ($trip->trip_status !== Trip::STATUS_PRE_START) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip has already started or ended.',
                    'http' => 422,
                ];
            }

            if (! in_array($booking->status, [
                Booking::STATUS_DRIVER_ASSIGNED,
                Booking::STATUS_DRIVER_ON_THE_WAY,
                Booking::STATUS_DRIVER_ARRIVED,
            ], true)) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Pickup acknowledgement is not available for this booking state.',
                    'http' => 422,
                ];
            }

            $previousBookingStatus = $booking->status;

            if (in_array($booking->status, [
                Booking::STATUS_DRIVER_ASSIGNED,
                Booking::STATUS_DRIVER_ON_THE_WAY,
            ], true)) {
                $booking->status = Booking::STATUS_DRIVER_ARRIVED;
            }

            $booking->passenger_ack_pickup_at = now();
            $booking->save();

            $this->scheduleFirebaseProjection($trip->id);

            $this->audit->log(
                actor: $passengerUser,
                objectType: 'BOOKING',
                objectId: $booking->id,
                action: 'BOOKING_PASSENGER_ACK_PICKUP',
                previous: ['status' => $previousBookingStatus],
                next: ['status' => $booking->status],
            );

            return ['booking' => $booking->fresh()];
        });
    }

    /**
     * Passenger confirms arrival at their destination during an active trip (UI signal; does not end the trip).
     *
     * @return array{booking: Booking}|array{error: string, message: string, http: int}
     */
    public function passengerAckDropoff(User $passengerUser, Booking $booking): array
    {
        if ((int) $booking->passenger_id !== (int) $passengerUser->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Booking does not belong to this passenger.', 'http' => 403];
        }

        return DB::transaction(function () use ($passengerUser, $booking) {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $trip = Trip::query()->lockForUpdate()->where('booking_id', $booking->id)->first();

            if ($trip === null) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'No trip exists for this booking.',
                    'http' => 422,
                ];
            }

            if ($booking->status !== Booking::STATUS_TRIP_IN_PROGRESS) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip is not in progress.',
                    'http' => 422,
                ];
            }

            if ($trip->trip_status !== Trip::STATUS_IN_PROGRESS) {
                return [
                    'error' => 'INVALID_STATE',
                    'message' => 'Trip is not active.',
                    'http' => 422,
                ];
            }

            $booking->passenger_ack_dropoff_at = now();
            $booking->save();

            $this->scheduleFirebaseProjection($trip->id);

            $this->audit->log(
                actor: $passengerUser,
                objectType: 'BOOKING',
                objectId: $booking->id,
                action: 'BOOKING_PASSENGER_ACK_DROPOFF',
                next: ['passenger_ack_dropoff_at' => $booking->passenger_ack_dropoff_at?->toIso8601String()],
            );

            return ['booking' => $booking->fresh()];
        });
    }

    /**
     * Mark trip aborted when booking is cancelled mid-trip.
     */
    public function abortTripIfExists(Booking $booking): void
    {
        $trip = Trip::query()->where('booking_id', $booking->id)->first();
        if ($trip === null) {
            return;
        }

        if (in_array($trip->trip_status, [Trip::STATUS_COMPLETED, Trip::STATUS_ABORTED], true)) {
            return;
        }

        $trip->trip_status = Trip::STATUS_ABORTED;
        $trip->ended_at = now();
        $trip->save();

        $this->scheduleFirebaseProjection($trip->id);
    }
}
