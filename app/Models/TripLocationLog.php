<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLocationLog extends Model
{
    protected $fillable = [
        'trip_id',
        'driver_id',
        'latitude',
        'longitude',
        'accuracy_meters',
        'speed_mps',
        'heading_degrees',
        'captured_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy_meters' => 'decimal:2',
            'speed_mps' => 'decimal:2',
            'heading_degrees' => 'decimal:2',
            'captured_at' => 'datetime',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
