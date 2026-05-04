<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentRecordService
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * @param  array{amount: mixed, method?: string, recorded_by_role?: string, notes?: string|null}  $data
     * @return array{payment: Payment, receipt: Receipt}|array{error: string, message: string, http: int}
     */
    public function recordPaymentForBooking(User $user, Driver $driver, Booking $booking, array $data): array
    {
        if ((int) $booking->driver_id !== (int) $driver->id) {
            return ['error' => 'FORBIDDEN', 'message' => 'Booking is not assigned to this driver.', 'http' => 403];
        }

        $amount = $data['amount'];
        if (is_string($amount)) {
            $amount = trim($amount);
        }
        if (! is_numeric($amount) || (float) $amount <= 0) {
            return ['error' => 'VALIDATION_ERROR', 'message' => 'Amount must be a positive number.', 'http' => 422];
        }

        $method = strtoupper((string) ($data['method'] ?? 'CASH'));
        $recordedBy = strtoupper((string) ($data['recorded_by_role'] ?? 'DRIVER'));
        $notes = isset($data['notes']) ? (string) $data['notes'] : null;

        return DB::transaction(function () use ($user, $booking, $amount, $method, $recordedBy, $notes) {
            /** @var Booking $bookingLocked */
            $bookingLocked = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($bookingLocked->status !== Booking::STATUS_COMPLETED) {
                return [
                    'error' => 'CONFLICT_STATE',
                    'message' => 'Payment can only be recorded after the trip is completed.',
                    'http' => 422,
                ];
            }

            $existingReceipt = Receipt::query()->where('booking_id', $bookingLocked->id)->first();
            if ($existingReceipt !== null) {
                $payment = Payment::query()->where('booking_id', $bookingLocked->id)->firstOrFail();

                return ['payment' => $payment, 'receipt' => $existingReceipt];
            }

            $paidAt = now();

            $payment = Payment::query()->updateOrCreate(
                ['booking_id' => $bookingLocked->id],
                [
                    'amount' => $amount,
                    'currency' => 'PHP',
                    'method' => $method,
                    'status' => 'COMPLETED',
                    'paid_at' => $paidAt,
                    'recorded_by_role' => $recordedBy,
                    'notes' => $notes,
                ]
            );

            $passengerCount = Trip::query()->where('booking_id', $bookingLocked->id)->value('passenger_count');

            $payload = [
                'receipt_version' => 1,
                'booking_reference' => $bookingLocked->booking_reference,
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'method' => $payment->method,
                'paid_at' => $paidAt->utc()->toIso8601String(),
                'pickup_address' => $bookingLocked->pickup_address,
                'destination_address' => $bookingLocked->destination_address,
                'passenger_count' => $passengerCount,
            ];

            $receipt = Receipt::query()->create([
                'booking_id' => $bookingLocked->id,
                'receipt_number' => 'TMP-'.$bookingLocked->id,
                'receipt_payload_json' => $payload,
                'generated_at' => $paidAt,
            ]);
            $receipt->receipt_number = Receipt::buildReceiptNumber((int) $receipt->id);
            $receipt->save();

            $this->audit->log(
                actor: $user,
                objectType: 'BOOKING',
                objectId: $bookingLocked->id,
                action: 'PAYMENT_RECORDED',
                next: [
                    'payment_id' => $payment->id,
                    'amount' => (string) $payment->amount,
                    'currency' => $payment->currency,
                    'method' => $payment->method,
                    'receipt_number' => $receipt->receipt_number,
                ],
                reason: $notes,
            );

            return ['payment' => $payment->fresh(), 'receipt' => $receipt->fresh()];
        });
    }
}
