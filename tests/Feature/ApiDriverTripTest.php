<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function tripSampleBookingPayload(): array
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

function driverTripScopes(): array
{
    return [
        'booking:offer:read:self',
        'booking:accept:self',
        'trip:update:self',
        'trip:start:self',
        'trip:end:self',
        'passenger:add:self',
        'payment:record:self',
    ];
}

it('runs driver trip lifecycle after accept', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', tripSampleBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, driverTripScopes());

    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');

    $accept = $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ]);
    $accept->assertOk();
    $tripId = $accept->json('data.trip_id');
    expect($tripId)->not->toBeNull();

    $geo = ['latitude' => 7.1083, 'longitude' => 124.8295, 'accuracy_meters' => 5.0];

    $this->postJson("/api/v1/drivers/trips/{$tripId}/arrive", $geo)->assertOk();
    $this->postJson("/api/v1/drivers/trips/{$tripId}/start", $geo)->assertOk();

    $this->postJson("/api/v1/drivers/trips/{$tripId}/add-passengers", [
        'quantity' => 1,
        'notes' => 'Walk-in',
    ])->assertOk();

    $endGeo = ['latitude' => 7.1117, 'longitude' => 124.8419, 'accuracy_meters' => 8.0];
    $this->postJson("/api/v1/drivers/trips/{$tripId}/end", $endGeo)->assertOk();

    expect(Booking::query()->findOrFail($bid)->status)->toBe(Booking::STATUS_COMPLETED);

    $pay = $this->postJson("/api/v1/payments/{$bid}/record", [
        'amount' => '45.00',
        'method' => 'CASH',
        'recorded_by_role' => 'DRIVER',
        'notes' => 'Cash collected',
    ]);
    $pay->assertOk();
    $pay->assertJsonPath('data.receipt.receipt_number', fn ($v) => is_string($v) && str_starts_with($v, 'RCT-'));
});

it('lets passengers read trip tracking and sos', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', tripSampleBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, driverTripScopes());
    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    Sanctum::actingAs($passenger, ['booking:read:self', 'trip:read:self']);
    $track = $this->getJson("/api/v1/bookings/{$bid}/trip-tracking");
    $track->assertOk();
    $track->assertJsonPath('data.trip.trip_status', 'PRE_START');

    Sanctum::actingAs($passenger, ['sos:create:self']);
    $this->postJson('/api/v1/passenger/sos', [
        'booking_id' => $bid,
        'latitude' => 7.11,
        'longitude' => 124.83,
        'notes' => 'Test SOS',
    ])->assertOk();
});
