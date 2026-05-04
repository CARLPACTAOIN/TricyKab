<?php

use App\Models\Booking;
use App\Models\Tricycle;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function capacityBookingPayload(): array
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

it('rejects add-passengers that would exceed tricycle capacity', function () {
    Tricycle::query()->update(['capacity' => 1]);

    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);
    $bid = $this->postJson('/api/v1/bookings', capacityBookingPayload())->json('data.booking.id');

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self', 'booking:accept:self',
        'trip:update:self', 'trip:start:self', 'passenger:add:self',
    ]);
    $offer = $this->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $accept = $this->postJson("/api/v1/drivers/bookings/{$bid}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ])->assertOk();
    $tripId = $accept->json('data.trip_id');

    $geo = ['latitude' => 7.1083, 'longitude' => 124.8295, 'accuracy_meters' => 5.0];
    $this->postJson("/api/v1/drivers/trips/{$tripId}/arrive", $geo)->assertOk();
    $this->postJson("/api/v1/drivers/trips/{$tripId}/start", $geo)->assertOk();

    $resp = $this->postJson("/api/v1/drivers/trips/{$tripId}/add-passengers", [
        'quantity' => 1,
    ]);
    $resp->assertStatus(409);
    $resp->assertJsonPath('error.code', 'DRIVER_CAPACITY_EXCEEDED');

    expect(Booking::query()->findOrFail($bid)->status)->toBe(Booking::STATUS_TRIP_IN_PROGRESS);
});
