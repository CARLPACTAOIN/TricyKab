<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PassengerSosRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Models\SosAlert;
use App\Services\AuditLogger;

class PassengerSosController extends Controller
{
    public function store(PassengerSosRequest $request, AuditLogger $audit)
    {
        $user = $request->user();
        $validated = $request->validated();

        $bookingId = isset($validated['booking_id']) ? (int) $validated['booking_id'] : null;
        $booking = null;
        if ($bookingId !== null) {
            $booking = Booking::query()->with('trip')->find($bookingId);
            if ($booking === null) {
                return ApiResponse::error('RESOURCE_NOT_FOUND', 'Booking not found.', 404);
            }
            if ($booking->passenger_id !== $user->id) {
                return ApiResponse::error('FORBIDDEN', 'This booking does not belong to the authenticated passenger.', 403);
            }
        }

        $tripId = $booking?->trip?->id;

        $alert = SosAlert::query()->create([
            'booking_id' => $booking?->id,
            'trip_id' => $tripId,
            'passenger_id' => $user->id,
            'passenger_name' => $user->name,
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
            'created_at' => $alert->created_at?->utc()->toIso8601String(),
        ]);
    }
}
