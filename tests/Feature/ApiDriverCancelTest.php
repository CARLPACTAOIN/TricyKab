<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function driverCancelBookingPayload(): array
{
    return [
        'ride_type' => 'SHARED',
        'pickup' => [
            'latitude' => 7.1083,
            'longitude' => 124.8295,
            'address' => 'Kabacan Public Market',
        ],
        'destination' => [
            'latitude' => 7.1117,
            'longitude' => 124.8419,
            'address' => 'University of Southern Mindanao',
        ],
    ];
}

it('lets the assigned driver cancel a booking with PASSENGER_NO_SHOW', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', driverCancelBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, ['booking:offer:read:self', 'booking:accept:self']);
    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    Sanctum::actingAs($driverUser, ['booking:cancel:self']);
    $cancel = $this->postJson("/api/v1/drivers/bookings/{$bid}/cancel", [
        'reason_code' => 'PASSENGER_NO_SHOW',
        'notes' => 'Waited 7 minutes, no contact.',
    ]);
    $cancel->assertOk();
    $cancel->assertJsonPath('data.status', Booking::STATUS_NO_SHOW_PASSENGER);

    $booking = Booking::query()->findOrFail($bid);
    expect($booking->status)->toBe(Booking::STATUS_NO_SHOW_PASSENGER);
    expect($booking->cancelled_at)->not->toBeNull();
});

it('rejects driver cancel when booking is not in a cancellable state', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', driverCancelBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, ['booking:cancel:self']);

    $cancel = $this->postJson("/api/v1/drivers/bookings/{$bid}/cancel", [
        'reason_code' => 'OTHER',
    ]);
    $cancel->assertStatus(403);
});
