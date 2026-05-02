<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpChallenge extends Model
{
    protected $fillable = [
        'phone_number',
        'role_hint',
        'otp_hash',
        'expires_at',
        'verify_attempts',
        'resend_count',
        'locked_at',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'locked_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }
}
