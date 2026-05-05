<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class DriverProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $driver = $user?->driverProfile?->load(['toda', 'tricycle']);

        return ApiResponse::success([
            'driver' => [
                'id' => $driver?->id,
                'first_name' => $driver?->first_name,
                'last_name' => $driver?->last_name,
                'full_name' => $driver?->full_name,
                'contact_number' => $driver?->contact_number,
                'license_number' => $driver?->license_number,
                'status' => $driver?->status,
                'availability_status' => $driver?->availability_status,
                'rating' => $driver?->rating,
            ],
            'toda' => $driver?->toda ? [
                'id' => $driver->toda->id,
                'name' => $driver->toda->name,
            ] : null,
            'tricycle' => $driver?->tricycle ? [
                'id' => $driver->tricycle->id,
                'body_number' => $driver->tricycle->body_number,
                'plate_number' => $driver->tricycle->plate_number,
                'capacity' => $driver->tricycle->capacity,
                'registration_status' => $driver->tricycle->registration_status,
                'status' => $driver->tricycle->status,
            ] : null,
        ]);
    }
}

