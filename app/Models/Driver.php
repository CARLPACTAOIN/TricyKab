<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'license_number',
        'contact_number',
        'address',
        'photo',
        'rating',
        'status',
        'tricycle_id',
        'toda_id',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    // --- Relationships ---

    public function tricycle()
    {
        return $this->belongsTo(Tricycle::class);
    }

    public function toda()
    {
        return $this->belongsTo(Toda::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    // --- Accessors ---

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
