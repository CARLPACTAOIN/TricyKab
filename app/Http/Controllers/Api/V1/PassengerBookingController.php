<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CancelBookingRequest;
use App\Http\Requests\Api\V1\StoreBookingRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Services\PassengerBookingService;
use Illuminate\Http\Request;

class PassengerBookingController extends Controller
{
    public function store(StoreBookingRequest $request, PassengerBookingService $service)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $booking = $service->create($user, $request->validated());

        return ApiResponse::success([
            'booking' => $service->bookingToApiData($booking),
        ]);
    }

    public function index(Request $request, PassengerBookingService $service)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $query = Booking::query()->where('passenger_id', $user->id)->latest('id');

        if ($request->boolean('active')) {
            $query->active();
        }

        $bookings = $query->limit(50)->get()->map(fn (Booking $b) => $service->bookingToApiData($b))->values()->all();

        return ApiResponse::success(['bookings' => $bookings]);
    }

    public function show(Request $request, Booking $booking, PassengerBookingService $service)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($booking->passenger_id !== $user->id) {
            return ApiResponse::error(
                'FORBIDDEN',
                'This booking does not belong to the authenticated passenger.',
                403
            );
        }

        return ApiResponse::success([
            'booking' => $service->bookingToApiData($booking),
        ]);
    }

    public function cancel(CancelBookingRequest $request, Booking $booking, PassengerBookingService $service)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $result = $service->cancelByPassenger(
            $user,
            $booking,
            $request->validated('reason_code'),
            $request->validated('notes')
        );

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http']
            );
        }

        /** @var Booking $updated */
        $updated = $result['booking'];

        return ApiResponse::success([
            'booking_id' => $updated->id,
            'status' => $updated->status,
        ]);
    }
}
