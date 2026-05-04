<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    public static function buildReceiptNumber(int $receiptId): string
    {
        return sprintf('RCT-%s-%06d', now()->format('Y'), $receiptId);
    }

    protected $fillable = [
        'booking_id',
        'receipt_number',
        'receipt_payload_json',
        'generated_at',
    ];

    protected $casts = [
        'receipt_payload_json' => 'array',
        'generated_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
