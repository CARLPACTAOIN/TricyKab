<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone',
        'phone_verified_at',
        'status',
        'home_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'profile_photo_url',
        'toda_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Role Helpers ---

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLguAdmin(): bool
    {
        return $this->isAdmin() && is_null($this->toda_id);
    }

    public function isTodaAdmin(): bool
    {
        return $this->isAdmin() && !is_null($this->toda_id);
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isPassenger(): bool
    {
        return $this->role === 'passenger';
    }

    // --- Relationships ---

    public function bookingsAsPassenger()
    {
        return $this->hasMany(Booking::class, 'passenger_id');
    }

    public function driverProfile()
    {
        return $this->hasOne(Driver::class);
    }

    public function toda()
    {
        return $this->belongsTo(Toda::class);
    }
}
