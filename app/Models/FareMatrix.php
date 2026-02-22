<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FareMatrix extends Model
{
    protected $fillable = [
        'ride_type',
        'base_fare',
        'per_km_rate',
        'minimum_distance',
        'discount_percentage',
        'per_passenger_addon',
        'rush_hour_surcharge',
        'night_diff_percentage',
        'effective_date',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'minimum_distance' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'per_passenger_addon' => 'decimal:2',
        'rush_hour_surcharge' => 'decimal:2',
        'night_diff_percentage' => 'decimal:2',
        'effective_date' => 'date',
    ];

    // --- Constants ---

    const TYPE_SHARED = 'shared';
    const TYPE_SPECIAL = 'special';
    const TYPE_CARGO = 'cargo';

    // --- Scopes ---

    public function scopeEffective($query)
    {
        return $query->where('effective_date', '<=', now())->latest('effective_date');
    }

    public function scopeForType($query, string $rideType)
    {
        return $query->where('ride_type', $rideType);
    }
}
