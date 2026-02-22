<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'method',
        'amount',
        'transaction_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // --- Relationships ---

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
