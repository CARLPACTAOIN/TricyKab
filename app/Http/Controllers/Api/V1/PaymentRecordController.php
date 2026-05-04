<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RecordPaymentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Booking;
use App\Services\PaymentRecordService;

class PaymentRecordController extends Controller
{
    public function store(RecordPaymentRequest $request, Booking $booking, PaymentRecordService $payments)
    {
        $user = $request->user();
        $driver = $user->driverProfile;
        if ($driver === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        $result = $payments->recordPaymentForBooking($user, $driver, $booking, $request->validated());

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                $result['message'],
                $result['http'],
            );
        }

        $payment = $result['payment'];
        $receipt = $result['receipt'];

        return ApiResponse::success([
            'payment' => [
                'booking_id' => $payment->booking_id,
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'method' => $payment->method,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at?->utc()->toIso8601String(),
            ],
            'receipt' => [
                'receipt_number' => $receipt->receipt_number,
                'generated_at' => $receipt->generated_at?->utc()->toIso8601String(),
                'payload' => $receipt->receipt_payload_json,
            ],
        ]);
    }
}
