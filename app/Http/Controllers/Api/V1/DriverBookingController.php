<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Services\PassengerBookingService;
use Illuminate\Http\Request;

class DriverBookingController extends Controller
{
    /**
     * Assigned / active bookings for the authenticated driver (pickup + trip reconciliation).
     */
    public function index(Request $request, PassengerBookingService $service)
    {
        $driver = $request->user()->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        $query = Booking::query()
            ->with(['passenger'])
            ->where('driver_id', $driver->id)
            ->latest('id');

        if ($request->boolean('active')) {
            $query->whereIn('status', [
                Booking::STATUS_DRIVER_ASSIGNED,
                Booking::STATUS_DRIVER_ON_THE_WAY,
                Booking::STATUS_DRIVER_ARRIVED,
                Booking::STATUS_TRIP_IN_PROGRESS,
            ]);
        }

        $bookings = $query->limit(50)->get()->map(fn (Booking $b) => $service->bookingToDriverApiData($b))->values()->all();

        return ApiResponse::success(['bookings' => $bookings]);
    }

    public function show(Request $request, Booking $booking, PassengerBookingService $service)
    {
        $driver = $request->user()->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if ((int) $booking->driver_id !== (int) $driver->id) {
            return ApiResponse::error('FORBIDDEN', 'This booking is not assigned to the authenticated driver.', 403);
        }

        $booking->loadMissing(['passenger']);

        return ApiResponse::success([
            'booking' => $service->bookingToDriverApiData($booking),
        ]);
    }
}
