<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AcceptDriverBookingRequest;
use App\Http\Requests\Api\V1\DeclineDriverBookingRequest;
use App\Http\Requests\Api\V1\DriverCancelBookingRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Services\DriverDispatchService;
use Illuminate\Http\Request;

class DriverDispatchController extends Controller
{
    public function index(Request $request, DriverDispatchService $service)
    {
        $driver = $request->user()->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        return ApiResponse::success([
            'offers' => $service->listPendingOffers($driver)->all(),
        ]);
    }

    public function accept(AcceptDriverBookingRequest $request, Booking $booking, DriverDispatchService $service)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        $result = $service->acceptOffer(
            $user,
            $driver,
            $booking,
            (int) $request->validated('dispatch_attempt_id'),
            (int) $request->validated('candidate_id'),
        );

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
                $result['details'] ?? []
            );
        }

        /** @var Booking $updated */
        $updated = $result['booking']->loadMissing('trip');
        $graceUntil = $updated->accepted_at?->copy()->addMinute();

        return ApiResponse::success([
            'booking_id' => $updated->id,
            'trip_id' => $updated->trip?->id,
            'status' => $updated->status,
            'grace_cancel_expires_at' => $graceUntil?->utc()->toIso8601String(),
        ]);
    }

    public function decline(DeclineDriverBookingRequest $request, Booking $booking, DriverDispatchService $service)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        $result = $service->declineOffer(
            $user,
            $driver,
            $booking,
            (int) $request->validated('dispatch_attempt_id'),
            (int) $request->validated('candidate_id'),
            (string) $request->validated('reason_code'),
        );

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        return ApiResponse::success([
            'status' => $result['status'],
            'booking_id' => $booking->id,
        ]);
    }

    public function cancel(DriverCancelBookingRequest $request, Booking $booking, DriverDispatchService $service)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        $result = $service->cancelByDriver(
            $user,
            $driver,
            $booking,
            (string) $request->validated('reason_code'),
            $request->validated('notes'),
        );

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        $updated = $result['booking'];

        return ApiResponse::success([
            'booking_id' => $updated->id,
            'status' => $updated->status,
            'cancelled_at' => $updated->cancelled_at?->utc()->toIso8601String(),
        ]);
    }
}
