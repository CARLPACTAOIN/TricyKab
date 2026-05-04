<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

function sampleDriverBookingsPayload(): array
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

it('requires authentication for GET /api/v1/drivers/me/bookings', function () {
    $this->getJson('/api/v1/drivers/me/bookings')->assertUnauthorized();
});

it('lists assigned bookings for the authenticated driver', function () {
    $this->seed(DatabaseSeeder::class);

    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDriverBookingsPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self',
        'booking:accept:self',
        'booking:read:self',
    ]);

    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    $list = $this->getJson('/api/v1/drivers/me/bookings');
    $list->assertOk();
    $list->assertJsonPath('data.bookings.0.id', $bid);
    $list->assertJsonStructure([
        'data' => [
            'bookings' => [
                [
                    'passenger' => [
                        'display_name',
                        'initials',
                    ],
                ],
            ],
        ],
    ]);
});

it('filters active driver bookings when active=1', function () {
    $this->seed(DatabaseSeeder::class);
    config()->set('dispatch.max_candidates', 20);

    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDriverBookingsPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_10@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self',
        'booking:accept:self',
        'booking:read:self',
    ]);

    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    $active = $this->getJson('/api/v1/drivers/me/bookings?active=1');
    $active->assertOk();
    expect($active->json('data.bookings'))->toHaveCount(1);

    Booking::query()->whereKey($bid)->update(['status' => Booking::STATUS_COMPLETED]);

    $empty = $this->getJson('/api/v1/drivers/me/bookings?active=1');
    $empty->assertOk();
    expect($empty->json('data.bookings'))->toHaveCount(0);
});

it('returns 403 when driver requests another drivers booking', function () {
    $this->seed(DatabaseSeeder::class);

    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', sampleDriverBookingsPayload())->json('data.booking.id');

    $assignedDriver = User::query()->where('email', 'driver_3@tricykab.local')->firstOrFail();
    Sanctum::actingAs($assignedDriver, [
        'booking:offer:read:self',
        'booking:accept:self',
    ]);

    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    $otherDriver = User::query()->where('email', 'driver_4@tricykab.local')->firstOrFail();
    Sanctum::actingAs($otherDriver, ['booking:read:self']);

    $this->getJson("/api/v1/drivers/me/bookings/{$bid}")
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});
