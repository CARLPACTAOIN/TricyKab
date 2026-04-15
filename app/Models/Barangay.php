<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function originBookings()
    {
        return $this->hasMany(Booking::class, 'origin_barangay_id');
    }

    public function destinationBookings()
    {
        return $this->hasMany(Booking::class, 'destination_barangay_id');
    }

    public function standbyPoints()
    {
        return $this->hasMany(StandbyPoint::class);
    }
}
