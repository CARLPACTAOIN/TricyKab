<?php

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function ratingSampleBookingPayload(): array
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

/**
 * @return array{0: User, 1: int}
 */
function createRatingTestBooking(): array
{
    $passenger = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($passenger, ['booking:create']);

    $bookingId = (int) test()->postJson('/api/v1/bookings', ratingSampleBookingPayload())
        ->json('data.booking.id');

    return [$passenger, $bookingId];
}

function completeTripForRating(int $bookingId): int
{
    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, [
        'booking:offer:read:self',
        'booking:accept:self',
        'trip:update:self',
        'trip:start:self',
        'trip:end:self',
        'passenger:add:self',
        'payment:record:self',
    ]);

    $offer = test()->getJson('/api/v1/drivers/me/dispatch-offers')->json('data.offers.0');
    $accept = test()->postJson("/api/v1/drivers/bookings/{$bookingId}/accept", [
        'dispatch_attempt_id' => $offer['dispatch_attempt_id'],
        'candidate_id' => $offer['candidate_id'],
    ]);
    $tripId = (int) $accept->json('data.trip_id');

    $geo = ['latitude' => 7.1083, 'longitude' => 124.8295, 'accuracy_meters' => 5.0];
    test()->postJson("/api/v1/drivers/trips/{$tripId}/arrive", $geo)->assertOk();
    test()->postJson("/api/v1/drivers/trips/{$tripId}/start", $geo)->assertOk();
    test()->postJson("/api/v1/drivers/trips/{$tripId}/end", [
        'latitude' => 7.1117,
        'longitude' => 124.8419,
        'accuracy_meters' => 8.0,
    ])->assertOk();

    test()->postJson("/api/v1/payments/{$bookingId}/record", [
        'amount' => '45.00',
        'method' => 'CASH',
        'recorded_by_role' => 'DRIVER',
    ])->assertOk();

    return $tripId;
}

it('lets passenger rate driver after trip completion', function () {
    [$passenger, $bookingId] = createRatingTestBooking();
    $tripId = completeTripForRating($bookingId);

    Sanctum::actingAs($passenger, ['booking:read:self', 'trip:read:self', 'receipt:read:self']);

    $rate = test()->postJson("/api/v1/trips/{$tripId}/rate", ['rating' => 5], [
        'Idempotency-Key' => 'trip-rate-'.$tripId,
    ]);

    $rate->assertOk();
    $rate->assertJsonPath('success', true);
    $rate->assertJsonPath('data.rating', 5);
    $rate->assertJsonPath('data.idempotent', false);

    $trip = Trip::query()->findOrFail($tripId);
    expect($trip->rating)->toBe(5);
    expect($trip->rated_at)->not->toBeNull();

    $driver = Driver::query()->whereKey(Booking::query()->findOrFail($bookingId)->driver_id)->firstOrFail();
    expect((float) $driver->rating)->toBe(5.0);
});

it('returns idempotent success when trip already rated', function () {
    [$passenger, $bookingId] = createRatingTestBooking();
    $tripId = completeTripForRating($bookingId);

    Sanctum::actingAs($passenger, ['booking:read:self', 'trip:read:self', 'receipt:read:self']);

    test()->postJson("/api/v1/trips/{$tripId}/rate", ['rating' => 4], [
        'Idempotency-Key' => 'trip-rate-'.$tripId,
    ])->assertOk();

    $second = test()->postJson("/api/v1/trips/{$tripId}/rate", ['rating' => 1], [
        'Idempotency-Key' => 'trip-rate-'.$tripId.'-retry',
    ]);

    $second->assertOk();
    $second->assertJsonPath('data.rating', 4);
    $second->assertJsonPath('data.idempotent', true);

    expect(Trip::query()->findOrFail($tripId)->rating)->toBe(4);
});

it('rejects rating from non owning passenger', function () {
    [, $bookingId] = createRatingTestBooking();
    $tripId = completeTripForRating($bookingId);

    $other = User::factory()->create(['role' => 'passenger', 'status' => 'ACTIVE']);
    Sanctum::actingAs($other, ['trip:read:self']);

    test()->postJson("/api/v1/trips/{$tripId}/rate", ['rating' => 3])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('includes rating state on receipt payload', function () {
    [$passenger, $bookingId] = createRatingTestBooking();
    $tripId = completeTripForRating($bookingId);

    Sanctum::actingAs($passenger, ['booking:read:self', 'trip:read:self', 'receipt:read:self']);

    $receipt = test()->getJson("/api/v1/bookings/{$bookingId}/receipt");
    $receipt->assertOk();
    $receipt->assertJsonPath('data.trip_id', $tripId);
    $receipt->assertJsonPath('data.can_rate', true);
    $receipt->assertJsonPath('data.trip_rating', null);

    test()->postJson("/api/v1/trips/{$tripId}/rate", ['rating' => 5])->assertOk();

    $after = test()->getJson("/api/v1/bookings/{$bookingId}/receipt");
    $after->assertOk();
    $after->assertJsonPath('data.trip_rating', 5);
    $after->assertJsonPath('data.can_rate', false);
});
