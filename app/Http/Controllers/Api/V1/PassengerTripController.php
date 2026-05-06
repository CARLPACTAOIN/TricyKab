<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PassengerAckRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Models\Receipt;
use App\Services\TripService;
use Illuminate\Http\Request;

class PassengerTripController extends Controller
{
    public function tracking(Request $request, Booking $booking)
    {
        $user = $request->user();

        if ($booking->passenger_id !== $user->id) {
            return ApiResponse::error(
                'FORBIDDEN',
                'This booking does not belong to the authenticated passenger.',
                403
            );
        }

        $booking->load(['driver.user', 'driver.tricycle', 'trip']);

        $trip = $booking->trip;
        $lastLog = $trip
            ? $trip->locationLogs()->latest('captured_at')->first()
            : null;

        $receiptAvailable = Receipt::query()->where('booking_id', $booking->id)->exists();

        return ApiResponse::success([
            'receipt_available' => $receiptAvailable,
            'booking' => [
                'id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'status' => $booking->status,
                'fare_amount' => (string) $booking->fare_amount,
                'passenger_ack_pickup_at' => $booking->passenger_ack_pickup_at?->utc()->toIso8601String(),
                'passenger_ack_dropoff_at' => $booking->passenger_ack_dropoff_at?->utc()->toIso8601String(),
                'pickup' => [
                    'latitude' => $booking->pickup_lat !== null ? (string) $booking->pickup_lat : null,
                    'longitude' => $booking->pickup_lng !== null ? (string) $booking->pickup_lng : null,
                    'address' => $booking->pickup_address,
                ],
                'destination' => [
                    'latitude' => $booking->destination_lat !== null ? (string) $booking->destination_lat : null,
                    'longitude' => $booking->destination_lng !== null ? (string) $booking->destination_lng : null,
                    'address' => $booking->destination_address,
                ],
            ],
            'driver' => $booking->driver ? [
                'id' => $booking->driver->id,
                'full_name' => $booking->driver->full_name,
                'phone' => $booking->driver->user?->phone,
                'plate_number' => $booking->driver->tricycle?->plate_number,
            ] : null,
            'trip' => $trip ? [
                'id' => $trip->id,
                'trip_status' => $trip->trip_status,
                'passenger_count' => (int) $trip->passenger_count,
                'started_at' => $trip->started_at?->utc()->toIso8601String(),
                'ended_at' => $trip->ended_at?->utc()->toIso8601String(),
                'last_location' => $lastLog ? [
                    'latitude' => (string) $lastLog->latitude,
                    'longitude' => (string) $lastLog->longitude,
                    'captured_at' => $lastLog->captured_at?->utc()->toIso8601String(),
                ] : null,
            ] : null,
        ]);
    }

    public function passengerAck(PassengerAckRequest $request, Booking $booking, TripService $trips)
    {
        $user = $request->user();

        if ($booking->passenger_id !== $user->id) {
            return ApiResponse::error(
                'FORBIDDEN',
                'This booking does not belong to the authenticated passenger.',
                403
            );
        }

        $kind = $request->validated('kind');
        $result = $kind === 'pickup'
            ? $trips->passengerAckPickup($user, $booking)
            : $trips->passengerAckDropoff($user, $booking);

        if (isset($result['error'])) {
            return ApiResponse::error($result['error'], $result['message'], $result['http']);
        }

        /** @var Booking $b */
        $b = $result['booking'];

        return ApiResponse::success([
            'booking' => [
                'id' => $b->id,
                'status' => $b->status,
                'passenger_ack_pickup_at' => $b->passenger_ack_pickup_at?->utc()->toIso8601String(),
                'passenger_ack_dropoff_at' => $b->passenger_ack_dropoff_at?->utc()->toIso8601String(),
            ],
        ]);
    }

    public function receipt(Request $request, Booking $booking)
    {
        $user = $request->user();

        if ($booking->passenger_id !== $user->id) {
            return ApiResponse::error(
                'FORBIDDEN',
                'This booking does not belong to the authenticated passenger.',
                403
            );
        }

        $booking->load('receipt');

        if ($booking->receipt === null) {
            return ApiResponse::error(
                'RESOURCE_NOT_FOUND',
                'No receipt has been generated for this booking yet.',
                404
            );
        }

        $r = $booking->receipt;

        return ApiResponse::success([
            'receipt_number' => $r->receipt_number,
            'generated_at' => $r->generated_at?->utc()->toIso8601String(),
            'payload' => $r->receipt_payload_json,
        ]);
    }
}
