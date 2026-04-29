<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'booking_id',
        'driver_id',
        'reported_by_role',
        'reported_by_name',
        'dispute_type',
        'description',
        'status',
        'resolution_notes',
        'resolved_by_admin_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by_admin_id');
    }
}
