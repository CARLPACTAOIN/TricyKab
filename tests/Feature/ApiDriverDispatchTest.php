<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function sampleDispatchBookingPayload(): array
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

it('exposes pending offers to an active driver after a booking is created', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDispatchBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, ['booking:offer:read:self']);

    $r = $this->getJson('/api/v1/drivers/me/dispatch-offers');
    $r->assertOk();
    $r->assertJsonPath('data.offers.0.booking.id', $bid);
});

it('assigns the booking when a driver accepts an offer', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDispatchBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self',
        'booking:accept:self',
    ]);

    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');

    $accept = $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ]);

    $accept->assertOk();
    $accept->assertJsonPath('data.status', Booking::STATUS_DRIVER_ASSIGNED);

    expect(Booking::query()->findOrFail($bid)->status)->toBe(Booking::STATUS_DRIVER_ASSIGNED)
        ->and(Booking::query()->findOrFail($bid)->accepted_at)->not->toBeNull();
});

it('returns conflict when a second driver accepts after assignment', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDispatchBookingPayload())->json('data.booking.id');

    $driver0 = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    $driver1 = User::query()->where('email', 'driver_1@tricykab.local')->firstOrFail();

    Sanctum::actingAs($driver0, ['booking:offer:read:self', 'booking:accept:self']);
    $offer0 = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');

    Sanctum::actingAs($driver1, ['booking:offer:read:self', 'booking:accept:self']);
    $offer1 = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    expect($offer1['booking']['id'])->toBe($bid);

    Sanctum::actingAs($driver0, ['booking:offer:read:self', 'booking:accept:self']);
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer0['dispatch_attempt_id'],
        'candidate_id' => $offer0['candidate_id'],
    ])->assertOk();

    Sanctum::actingAs($driver1, ['booking:offer:read:self', 'booking:accept:self']);
    $late = $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer1['dispatch_attempt_id'],
        'candidate_id' => $offer1['candidate_id'],
    ]);

    $late->assertStatus(409);
    $late->assertJsonPath('error.code', 'DISPATCH_RACE_LOST');
});

it('records a decline without assigning the booking', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDispatchBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self',
        'booking:decline:self',
    ]);

    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');

    $decline = $this->postJson("/api/v1/drivers/bookings/{$bid}/decline", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
        'reason_code' => 'TOO_FAR',
    ]);

    $decline->assertOk();
    $decline->assertJsonPath('data.status', 'DECLINED');

    expect(Booking::query()->findOrFail($bid)->status)->toBe(Booking::STATUS_SEARCHING_DRIVER);
});

it('returns 403 for inactive drivers listing offers', function () {
    $inactive = User::query()->where('email', 'driver_inactive@tricykab.local')->firstOrFail();
    Sanctum::actingAs($inactive, ['booking:offer:read:self']);

    $this->getJson('/api/v1/drivers/me/dispatch-offers')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});
