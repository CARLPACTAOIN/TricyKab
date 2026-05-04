<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripPassengerEvent extends Model
{
    public const TYPE_ADDITIONAL_PASSENGER_ADDED = 'ADDITIONAL_PASSENGER_ADDED';

    protected $fillable = [
        'trip_id',
        'event_type',
        'quantity',
        'notes',
        'recorded_by_driver_id',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function recordedByDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'recorded_by_driver_id');
    }
}
