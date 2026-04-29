<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    protected $fillable = [
        'booking_id',
        'trip_id',
        'passenger_id',
        'passenger_name',
        'latitude',
        'longitude',
        'location_note',
        'status',
        'acknowledged_by_admin_id',
        'acknowledged_at',
        'closed_by_admin_id',
        'closed_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'acknowledged_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }
}
