<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DriverSosRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\SosAlert;
use App\Services\AuditLogger;

class DriverSosController extends Controller
{
    public function store(DriverSosRequest $request, AuditLogger $audit)
    {
        $user = $request->user();
        $validated = $request->validated();

        $driver = Driver::query()->where('user_id', $user->id)->first();
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile not found.', 403);
        }

        $bookingId = isset($validated['booking_id']) ? (int) $validated['booking_id'] : null;
        $booking = null;
        if ($bookingId !== null) {
            $booking = Booking::query()->with('trip')->find($bookingId);
            if ($booking === null) {
                return ApiResponse::error('RESOURCE_NOT_FOUND', 'Booking not found.', 404);
            }
            if ((int) $booking->driver_id !== (int) $driver->id) {
                return ApiResponse::error('FORBIDDEN', 'This booking is not assigned to you.', 403);
            }
        }

        $tripId = $booking?->trip?->id;
        $driverName = trim(($driver->first_name ?? '').' '.($driver->last_name ?? ''));
        if ($driverName === '') {
            $driverName = $user->name ?? 'Driver';
        }

        $alert = SosAlert::query()->create([
            'booking_id' => $booking?->id,
            'trip_id' => $tripId,
            'reporter_role' => 'DRIVER',
            'driver_id' => $driver->id,
            'driver_name' => $driverName,
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'location_note' => $validated['notes'] ?? null,
            'status' => 'OPEN',
        ]);

        $audit->log(
            actor: $user,
            objectType: 'SOS_ALERT',
            objectId: $alert->id,
            action: 'SOS_RAISED',
            next: [
                'booking_id' => $booking?->id,
                'trip_id' => $tripId,
                'reporter_role' => 'DRIVER',
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
            ],
            reason: $validated['notes'] ?? null,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return ApiResponse::success([
            'sos_alert_id' => $alert->id,
            'status' => $alert->status,
            'reporter_role' => 'DRIVER',
            'created_at' => $alert->created_at?->utc()->toIso8601String(),
        ]);
    }
}
