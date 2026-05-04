<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function idempotencyBookingPayload(): array
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

it('replays cached response for the same idempotency key', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);

    $key = (string) Str::uuid();

    $first = $this->withHeaders(['Idempotency-Key' => $key])
        ->postJson('/api/v1/bookings', idempotencyBookingPayload());
    $first->assertOk();
    $bookingIdFirst = $first->json('data.booking.id');

    $second = $this->withHeaders(['Idempotency-Key' => $key])
        ->postJson('/api/v1/bookings', idempotencyBookingPayload());
    $second->assertOk();

    expect($second->json('data.booking.id'))->toBe($bookingIdFirst);
    expect(Booking::query()->where('passenger_id', $passenger->id)->count())->toBe(1);
});

it('does not de-duplicate when no idempotency header is sent', function () {
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);

    $this->postJson('/api/v1/bookings', idempotencyBookingPayload())->assertOk();
    $this->postJson('/api/v1/bookings', idempotencyBookingPayload())->assertOk();

    expect(Booking::query()->where('passenger_id', $passenger->id)->count())->toBe(2);
});
