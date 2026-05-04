<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDispatchCandidate extends Model
{
    public const RESPONSE_PENDING = 'PENDING';

    public const RESPONSE_ACCEPTED = 'ACCEPTED';

    public const RESPONSE_DECLINED = 'DECLINED';

    public const RESPONSE_EXPIRED = 'EXPIRED';

    public const RESPONSE_LOST_RACE = 'LOST_RACE';

    protected $fillable = [
        'dispatch_attempt_id',
        'driver_id',
        'rank_order',
        'distance_meters',
        'standby_score',
        'fairness_score',
        'total_score',
        'response_status',
        'responded_at',
        'decline_reason_code',
    ];

    protected function casts(): array
    {
        return [
            'standby_score' => 'decimal:4',
            'fairness_score' => 'decimal:4',
            'total_score' => 'decimal:4',
            'responded_at' => 'datetime',
        ];
    }

    public function dispatchAttempt(): BelongsTo
    {
        return $this->belongsTo(BookingDispatchAttempt::class, 'dispatch_attempt_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
