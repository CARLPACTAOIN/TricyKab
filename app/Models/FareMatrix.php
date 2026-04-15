<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FareMatrix extends Model
{
    protected $fillable = [
        'ride_type',
        'base_fare',
        'per_km_rate',
        'multiplier',
        'min_fare',
        'max_fare',
        'effective_date',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'multiplier' => 'decimal:4',
        'min_fare' => 'decimal:2',
        'max_fare' => 'decimal:2',
        'effective_date' => 'date',
    ];

    // --- Constants ---

    const TYPE_SHARED = 'shared';
    const TYPE_SPECIAL = 'special';

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
