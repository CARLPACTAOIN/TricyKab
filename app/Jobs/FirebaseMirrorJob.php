<?php

namespace App\Jobs;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FirebaseMirrorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tripId
    ) {}

    public function handle(): void
    {
        if (! config('services.firebase.projection_enabled', false)) {
            return;
        }

        $trip = Trip::query()->find($this->tripId);
        if ($trip === null) {
            return;
        }

        Log::debug('firebase.mirror.stub', [
            'trip_id' => $trip->id,
            'booking_id' => $trip->booking_id,
        ]);
    }
}
