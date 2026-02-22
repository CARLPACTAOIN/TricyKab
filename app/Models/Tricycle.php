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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
