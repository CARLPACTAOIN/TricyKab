<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'passenger_id',
        'driver_id',
        'tricycle_id',
        'pickup_lat',
        'pickup_lng',
        'pickup_address',
        'destination_lat',
        'destination_lng',
        'destination_address',
        'ride_type',
        'status',
        'fare_amount',
        'distance_km',
        'accepted_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'pickup_lat' => 'decimal:7',
        'pickup_lng' => 'decimal:7',
        'destination_lat' => 'decimal:7',
        'destination_lng' => 'decimal:7',
        'fare_amount' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // --- Status Constants ---

    const STATUS_CREATED = 'CREATED';
    const STATUS_SEARCHING_DRIVER = 'SEARCHING_DRIVER';
    const STATUS_DRIVER_ASSIGNED = 'DRIVER_ASSIGNED';
    const STATUS_DRIVER_ON_THE_WAY = 'DRIVER_ON_THE_WAY';
    const STATUS_DRIVER_ARRIVED = 'DRIVER_ARRIVED';
    const STATUS_TRIP_IN_PROGRESS = 'TRIP_IN_PROGRESS';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED_BY_PASSENGER = 'CANCELLED_BY_PASSENGER';
    const STATUS_CANCELLED_BY_DRIVER = 'CANCELLED_BY_DRIVER';
    const STATUS_CANCELLED_NO_DRIVER = 'CANCELLED_NO_DRIVER';
    const STATUS_NO_SHOW_DRIVER = 'NO_SHOW_DRIVER';
    const STATUS_NO_SHOW_PASSENGER = 'NO_SHOW_PASSENGER';

    // --- Relationships ---

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function tricycle()
    {
        return $this->belongsTo(Tricycle::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_CREATED,
            self::STATUS_SEARCHING_DRIVER,
            self::STATUS_DRIVER_ASSIGNED,
            self::STATUS_DRIVER_ON_THE_WAY,
            self::STATUS_DRIVER_ARRIVED,
            self::STATUS_TRIP_IN_PROGRESS,
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // --- Helpers ---

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_CREATED,
            self::STATUS_SEARCHING_DRIVER,
            self::STATUS_DRIVER_ASSIGNED,
            self::STATUS_DRIVER_ON_THE_WAY,
            self::STATUS_DRIVER_ARRIVED,
            self::STATUS_TRIP_IN_PROGRESS,
        ]);
    }

    public function isCancelled(): bool
    {
        return str_starts_with($this->status, 'CANCELLED_')
            || str_starts_with($this->status, 'NO_SHOW_');
    }
}
