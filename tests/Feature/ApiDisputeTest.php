<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function disputeSampleBookingPayload(): array
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

function driverDisputeScopes(): array
{
    return [
        'booking:offer:read:self',
        'booking:accept:self',
        'booking:read:self',
        'trip:update:self',
        'trip:start:self',
        'trip:end:self',
        'passenger:add:self',
        'payment:record:self',
        'dispute:create:self',
    ];
}

function completeBookingForDispute(int $bookingId): void
{
    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, driverDisputeScopes());
    $offer = test()->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $accept = test()->postJson("/api/v1/drivers/bookings/{$bookingId}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ]);
    $accept->assertOk();
    $tripId = $accept->json('data.trip_id');
    $geo = ['latitude' => 7.1083, 'longitude' => 124.8295, 'accuracy_meters' => 5.0];
    test()->postJson("/api/v1/drivers/trips/{$tripId}/arrive", $geo)->assertOk();
    test()->postJson("/api/v1/drivers/trips/{$tripId}/start", $geo)->assertOk();
    test()->postJson("/api/v1/drivers/trips/{$tripId}/end", $geo)->assertOk();
}

it('lets passenger file dispute on completed booking', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create', 'booking:read:self', 'dispute:create:self']);
    $bid = $this->postJson('/api/v1/bookings', disputeSampleBookingPayload())->json('data.booking.id');

    completeBookingForDispute($bid);

    Sanctum::actingAs($passenger, ['booking:read:self', 'dispute:create:self']);
    $response = $this->postJson("/api/v1/bookings/{$bid}/dispute", [
        'dispute_type' => 'FARE',
        'description' => 'Fare charged was higher than estimate shown.',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.dispute_type', 'FARE');
    $response->assertJsonPath('data.reported_by_role', 'PASSENGER');
});

it('lets driver file dispute on assigned booking', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', disputeSampleBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, driverDisputeScopes());
    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();

    $response = $this->postJson("/api/v1/bookings/{$bid}/dispute", [
        'dispute_type' => 'NO_SHOW',
        'description' => 'Passenger was not at pickup point after waiting.',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.reported_by_role', 'DRIVER');
});

it('rejects dispute on created booking', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create', 'booking:read:self', 'dispute:create:self']);
    $bid = $this->postJson('/api/v1/bookings', disputeSampleBookingPayload())->json('data.booking.id');

    $booking = Booking::query()->findOrFail($bid);
    $booking->update(['status' => Booking::STATUS_CREATED]);

    $response = $this->postJson("/api/v1/bookings/{$bid}/dispute", [
        'dispute_type' => 'OTHER',
        'description' => 'Trying to dispute before trip started.',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('error.code', 'BOOKING_NOT_DISPUTABLE');
});

it('forbids non-participant from filing dispute', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', disputeSampleBookingPayload())->json('data.booking.id');

    completeBookingForDispute($bid);

    $other = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($other, ['dispute:create:self']);

    $response = $this->postJson("/api/v1/bookings/{$bid}/dispute", [
        'dispute_type' => 'FARE',
        'description' => 'This passenger was not on the trip.',
    ]);

    $response->assertForbidden();
    $response->assertJsonPath('error.code', 'FORBIDDEN');
});
