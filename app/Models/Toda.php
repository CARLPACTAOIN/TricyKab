<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toda extends Model
{
    protected $fillable = [
        'name',
        'area_coverage',
        'operating_hours',
        'status',
    ];

    // --- Relationships ---

    public function tricycles()
    {
        return $this->hasMany(Tricycle::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function standbyPoints()
    {
        return $this->hasMany(StandbyPoint::class);
    }

    // --- Accessors ---

    public function getDriverCountAttribute(): int
    {
        return $this->drivers()->count();
    }

    public function getTricycleCountAttribute(): int
    {
        return $this->tricycles()->count();
    }
}
