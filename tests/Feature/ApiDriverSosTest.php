<?php

use App\Models\SosAlert;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function driverSosBookingPayload(): array
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

it('lets driver create sos alert with gps', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', driverSosBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self',
        'booking:accept:self',
        'sos:create:self',
    ]);
    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    $response = $this->postJson('/api/v1/drivers/sos', [
        'booking_id' => $bid,
        'latitude' => 7.11,
        'longitude' => 124.83,
        'notes' => 'Driver emergency test',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.reporter_role', 'DRIVER');

    $alert = SosAlert::query()->latest('id')->first();
    expect($alert)->not->toBeNull();
    expect($alert->reporter_role)->toBe('DRIVER');
    expect($alert->driver_id)->not->toBeNull();
});

it('forbids passenger from driver sos endpoint', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['sos:create:self']);

    $response = $this->postJson('/api/v1/drivers/sos', [
        'latitude' => 7.11,
        'longitude' => 124.83,
    ]);

    $response->assertForbidden();
});
