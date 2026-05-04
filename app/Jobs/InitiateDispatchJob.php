<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\BookingDispatchAttempt;
use App\Models\BookingDispatchCandidate;
use App\Models\Driver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

/**
 * PRD dispatch MVP: create an open attempt, rank active drivers, and write candidate rows.
 */
class InitiateDispatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $bookingId
    ) {}

    public function handle(): void
    {
        $booking = Booking::query()->find($this->bookingId);
        if ($booking === null || $booking->status !== Booking::STATUS_SEARCHING_DRIVER) {
            return;
        }

        DB::transaction(function () use ($booking): void {
            $booking->refresh();

            if ($booking->status !== Booking::STATUS_SEARCHING_DRIVER) {
                return;
            }

            $maxCandidates = max(0, (int) config('dispatch.max_candidates', 5));
            $ttlSeconds = max(1, (int) config('dispatch.offer_ttl_seconds', 60));
            $radiusMeters = max(1, (int) config('booking.search_radius_meters', 1000));

            $attemptNo = (int) BookingDispatchAttempt::query()
                ->where('booking_id', $booking->id)
                ->max('attempt_no');
            $attemptNo++;

            $drivers = Driver::query()
                ->whereNotNull('user_id')
                ->whereRaw('LOWER(status) = ?', ['active'])
                ->where('availability_status', Driver::AVAILABILITY_ONLINE)
                ->orderBy('id')
                ->limit($maxCandidates)
                ->get();

            $started = now();
            $expires = $started->copy()->addSeconds($ttlSeconds);

            $attempt = BookingDispatchAttempt::query()->create([
                'booking_id' => $booking->id,
                'attempt_no' => $attemptNo,
                'search_radius_meters' => $radiusMeters,
                'broadcast_started_at' => $started,
                'broadcast_expires_at' => $expires,
                'candidate_count' => $drivers->count(),
                'status' => BookingDispatchAttempt::STATUS_OPEN,
            ]);

            foreach ($drivers as $index => $driver) {
                BookingDispatchCandidate::query()->create([
                    'dispatch_attempt_id' => $attempt->id,
                    'driver_id' => $driver->id,
                    'rank_order' => $index + 1,
                    'distance_meters' => 0,
                    'standby_score' => '1.0000',
                    'fairness_score' => '1.0000',
                    'total_score' => '1.0000',
                    'response_status' => BookingDispatchCandidate::RESPONSE_PENDING,
                ]);
            }
        });
    }
}
