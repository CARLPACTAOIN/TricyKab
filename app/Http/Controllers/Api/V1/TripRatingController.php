<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Trip;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * PRD §3.1 / §7.14 — Passenger rates the driver after trip completion.
 *
 * POST /api/v1/trips/{trip}/rate
 *
 * Idempotent: if the trip already has a rating, returns current state (200)
 * without overwriting. Protected by the idempotency middleware on the route.
 */
class TripRatingController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    public function store(Request $request, Trip $trip): JsonResponse
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Ownership check: only the passenger who made the booking can rate
        if ((int) $trip->booking?->passenger_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'You are not authorised to rate this trip.',
                ],
            ], 403);
        }

        // PRD §7.14 — trip must be in a terminal completed state to accept a rating
        if ($trip->booking?->status !== \App\Models\Booking::STATUS_COMPLETED) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TRIP_NOT_COMPLETED',
                    'message' => 'Ratings can only be submitted for completed trips.',
                ],
            ], 422);
        }

        // Idempotency: already rated — return current state, no re-write
        if ($trip->rated_at !== null) {
            return response()->json([
                'success' => true,
                'data' => [
                    'trip_id' => $trip->id,
                    'rating' => $trip->rating,
                    'rated_at' => $trip->rated_at->toIso8601String(),
                    'driver_rating_avg' => $trip->booking?->driver?->rating,
                    'idempotent' => true,
                ],
            ]);
        }

        $rating = (int) $request->input('rating');

        return DB::transaction(function () use ($trip, $rating, $user): JsonResponse {
            $trip->rating = $rating;
            $trip->rated_at = now();
            $trip->save();

            // Recalculate driver's average from all rated trips for this driver
            $driverAvg = null;
            $driver = $trip->booking?->driver;

            if ($driver !== null) {
                $avg = Trip::query()
                    ->whereHas('booking', fn ($q) => $q->where('driver_id', $driver->id))
                    ->whereNotNull('rating')
                    ->avg('rating');

                $driverAvg = $avg !== null ? round((float) $avg, 2) : null;

                // PRD §7.5 — persist updated rating_avg on driver record
                Driver::query()->whereKey($driver->id)->update(['rating' => $driverAvg ?? $driver->rating]);
            }

            $this->audit->log(
                actor: $user,
                objectType: 'TRIP',
                objectId: $trip->id,
                action: 'TRIP_RATED',
                previous: ['rating' => null],
                next: ['rating' => $rating, 'driver_rating_avg' => $driverAvg],
                reason: 'Passenger submitted rating',
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'trip_id' => $trip->id,
                    'rating' => $rating,
                    'rated_at' => $trip->rated_at->toIso8601String(),
                    'driver_rating_avg' => $driverAvg,
                    'idempotent' => false,
                ],
            ]);
        });
    }
}
