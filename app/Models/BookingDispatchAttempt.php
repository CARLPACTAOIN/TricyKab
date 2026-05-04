<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingDispatchAttempt extends Model
{
    public const STATUS_OPEN = 'OPEN';

    public const STATUS_ASSIGNED = 'ASSIGNED';

    public const STATUS_EXPIRED = 'EXPIRED';

    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'booking_id',
        'attempt_no',
        'search_radius_meters',
        'broadcast_started_at',
        'broadcast_expires_at',
        'candidate_count',
        'winner_driver_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'broadcast_started_at' => 'datetime',
            'broadcast_expires_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function winnerDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'winner_driver_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(BookingDispatchCandidate::class, 'dispatch_attempt_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
