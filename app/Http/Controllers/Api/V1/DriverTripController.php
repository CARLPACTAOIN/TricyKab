<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DriverTripAddPassengersRequest;
use App\Http\Requests\Api\V1\DriverTripGeoRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Trip;
use App\Services\TripService;

class DriverTripController extends Controller
{
    public function arrive(DriverTripGeoRequest $request, Trip $trip, TripService $trips)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if ((int) $trip->driver_id !== (int) $driver->id) {
            return ApiResponse::error('FORBIDDEN', 'This trip is not assigned to the authenticated driver.', 403);
        }

        $validated = $request->validated();
        $result = $trips->recordArrival($user, $driver, $trip, [
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'accuracy_meters' => isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
        ]);

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        return ApiResponse::success([
            'trip' => $this->tripToApiData($result['trip']),
        ]);
    }

    public function start(DriverTripGeoRequest $request, Trip $trip, TripService $trips)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if ((int) $trip->driver_id !== (int) $driver->id) {
            return ApiResponse::error('FORBIDDEN', 'This trip is not assigned to the authenticated driver.', 403);
        }

        $validated = $request->validated();
        $result = $trips->startTrip($user, $driver, $trip, [
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'accuracy_meters' => isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
            'started_at_client' => $validated['started_at_client'] ?? null,
        ]);

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        return ApiResponse::success([
            'trip' => $this->tripToApiData($result['trip']),
        ]);
    }

    public function addPassengers(DriverTripAddPassengersRequest $request, Trip $trip, TripService $trips)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if ((int) $trip->driver_id !== (int) $driver->id) {
            return ApiResponse::error('FORBIDDEN', 'This trip is not assigned to the authenticated driver.', 403);
        }

        $validated = $request->validated();
        $result = $trips->addPassengers($user, $driver, $trip, [
            'quantity' => (int) $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        return ApiResponse::success([
            'trip' => $this->tripToApiData($result['trip']),
        ]);
    }

    public function end(DriverTripGeoRequest $request, Trip $trip, TripService $trips)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if ((int) $trip->driver_id !== (int) $driver->id) {
            return ApiResponse::error('FORBIDDEN', 'This trip is not assigned to the authenticated driver.', 403);
        }

        $validated = $request->validated();
        $result = $trips->endTrip($user, $driver, $trip, [
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'accuracy_meters' => isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
            'ended_at_client' => $validated['ended_at_client'] ?? null,
            'manual_reason' => $validated['manual_reason'] ?? null,
        ]);

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        return ApiResponse::success([
            'trip' => $this->tripToApiData($result['trip']),
        ]);
    }

    public function location(DriverTripGeoRequest $request, Trip $trip, TripService $trips)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if ((int) $trip->driver_id !== (int) $driver->id) {
            return ApiResponse::error('FORBIDDEN', 'This trip is not assigned to the authenticated driver.', 403);
        }

        $validated = $request->validated();
        $trips->appendDriverLocation($driver, $trip, [
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'accuracy_meters' => isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
        ]);

        return ApiResponse::success([
            'trip_id' => $trip->id,
            'recorded_at' => now()->utc()->toIso8601String(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function tripToApiData(Trip $trip): array
    {
        return [
            'id' => $trip->id,
            'booking_id' => $trip->booking_id,
            'trip_status' => $trip->trip_status,
            'passenger_count' => (int) $trip->passenger_count,
            'started_at' => $trip->started_at?->utc()->toIso8601String(),
            'ended_at' => $trip->ended_at?->utc()->toIso8601String(),
            'start_latitude' => $trip->start_latitude !== null ? (string) $trip->start_latitude : null,
            'start_longitude' => $trip->start_longitude !== null ? (string) $trip->start_longitude : null,
            'end_latitude' => $trip->end_latitude !== null ? (string) $trip->end_latitude : null,
            'end_longitude' => $trip->end_longitude !== null ? (string) $trip->end_longitude : null,
        ];
    }
}
