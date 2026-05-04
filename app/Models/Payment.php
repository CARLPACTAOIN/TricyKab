<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const STATUS_PENDING = 'PENDING';

    public const STATUS_COMPLETED = 'COMPLETED';

    protected $fillable = [
        'booking_id',
        'method',
        'amount',
        'currency',
        'transaction_id',
        'status',
        'paid_at',
        'recorded_by_role',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // --- Relationships ---

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
