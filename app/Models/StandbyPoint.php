<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyPoint extends Model
{
    protected $fillable = [
        'name',
        'toda_id',
        'barangay_id',
        'latitude',
        'longitude',
        'radius_meters',
        'priority_weight',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'priority_weight' => 'decimal:2',
    ];

    public function toda()
    {
        return $this->belongsTo(Toda::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}
