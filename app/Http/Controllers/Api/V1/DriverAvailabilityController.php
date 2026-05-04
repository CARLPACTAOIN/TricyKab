<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreDriverAvailabilityRequest;
use App\Http\Responses\ApiResponse;
use App\Services\DriverAvailabilityService;

class DriverAvailabilityController extends Controller
{
    public function store(StoreDriverAvailabilityRequest $request, DriverAvailabilityService $service)
    {
        $driver = $request->user()->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        $validated = $request->validated();
        $updated = $service->updateFromRequest($driver, [
            'driver_status' => $validated['driver_status'],
            'latitude' => isset($validated['latitude']) ? (float) $validated['latitude'] : null,
            'longitude' => isset($validated['longitude']) ? (float) $validated['longitude'] : null,
            'accuracy_meters' => isset($validated['accuracy_meters']) ? (float) $validated['accuracy_meters'] : null,
        ]);

        return ApiResponse::success([
            'driver_id' => $updated->id,
            'driver_status' => $updated->availability_status,
            'effective_at' => $updated->last_availability_at?->utc()->toIso8601String(),
        ]);
    }
}
