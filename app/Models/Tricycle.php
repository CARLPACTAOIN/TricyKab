<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tricycle extends Model
{
    protected $fillable = [
        'body_number',
        'plate_number',
        'toda_id',
        'make_model',
        'status',
        'registration_status',
        'capacity',
    ];

    // --- Relationships ---

    public function toda()
    {
        return $this->belongsTo(Toda::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * A tricycle may be associated with multiple drivers (e.g. rental/shift use).
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
