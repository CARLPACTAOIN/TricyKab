<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    public const STATUS_PRE_START = 'PRE_START';

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_ABORTED = 'ABORTED';

    protected $fillable = [
        'booking_id',
        'driver_id',
        'passenger_id',
        'trip_status',
        'started_at',
        'ended_at',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'computed_distance_meters',
        'computed_duration_seconds',
        'passenger_count',
        'detour_seconds_over_initial_eta',
        'gps_quality_status',
        'rating',
        'rated_at',
        'end_method',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'rated_at' => 'datetime',
            'start_latitude' => 'decimal:7',
            'start_longitude' => 'decimal:7',
            'end_latitude' => 'decimal:7',
            'end_longitude' => 'decimal:7',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function locationLogs(): HasMany
    {
        return $this->hasMany(TripLocationLog::class);
    }

    public function passengerEvents(): HasMany
    {
        return $this->hasMany(TripPassengerEvent::class);
    }
}
