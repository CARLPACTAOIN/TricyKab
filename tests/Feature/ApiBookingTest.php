<?php

use App\Models\Booking;
use App\Models\BookingDispatchAttempt;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

function makePassengerBookingForCancelTests(User $passenger): Booking
{
    return Booking::query()->create([
        'passenger_id' => $passenger->id,
        'pickup_lat' => 7.1083,
        'pickup_lng' => 124.8295,
        'pickup_address' => 'Kabacan Public Market',
        'destination_lat' => 7.1117,
        'destination_lng' => 124.8419,
        'destination_address' => 'University of Southern Mindanao',
        'ride_type' => 'shared',
        'status' => Booking::STATUS_SEARCHING_DRIVER,
        'fare_amount' => 25.00,
        'distance_km' => 1.50,
    ]);
}

function sampleBookingPayload(): array
{
    return [
        'ride_type' => 'SHARED',
        'pickup' => [
            'latitude' => 7.1083,
            'longitude' => 124.8295,
            'address' => 'Kabacan Public Market',
            'notes' => 'Near south gate',
        ],
        'destination' => [
            'latitude' => 7.1117,
            'longitude' => 124.8419,
            'address' => 'University of Southern Mindanao',
        ],
    ];
}

it('requires authentication for POST /api/v1/bookings', function () {
    $this->postJson('/api/v1/bookings', sampleBookingPayload())
        ->assertUnauthorized();
});

it('forbids non-passenger clients', function () {
    $driver = User::factory()->create([
        'role' => 'driver',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($driver, ['booking:create']);

    $this->postJson('/api/v1/bookings', sampleBookingPayload())
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('forbids passenger token without booking:create ability', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:read:self']);

    $this->postJson('/api/v1/bookings', sampleBookingPayload())
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('creates a shared booking and returns prd-shaped json', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create']);

    $response = $this->postJson('/api/v1/bookings', sampleBookingPayload());

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.booking.status', Booking::STATUS_SEARCHING_DRIVER);
    $response->assertJsonPath('data.booking.ride_type', 'SHARED');
    $response->assertJsonStructure([
        'data' => [
            'booking' => [
                'id',
                'reference',
                'status',
                'ride_type',
                'estimated_distance_meters',
                'estimated_duration_seconds',
                'estimated_fare',
                'search_radius_meters',
                'pickup',
                'destination',
                'driver_id',
                'created_at',
            ],
        ],
    ]);

    $id = $response->json('data.booking.id');
    expect($id)->toBeInt();

    $booking = Booking::query()->findOrFail($id);
    expect($booking->passenger_id)->toBe($passenger->id)
        ->and($booking->status)->toBe(Booking::STATUS_SEARCHING_DRIVER)
        ->and($booking->ride_type)->toBe('shared')
        ->and($booking->pickup_notes)->toBe('Near south gate');
});

it('creates a special ride booking', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create']);

    $payload = sampleBookingPayload();
    $payload['ride_type'] = 'SPECIAL';

    $response = $this->postJson('/api/v1/bookings', $payload);

    $response->assertOk();
    $response->assertJsonPath('data.booking.ride_type', 'SPECIAL');

    $booking = Booking::query()->findOrFail($response->json('data.booking.id'));
    expect($booking->ride_type)->toBe('special');
});

it('requires authentication for POST /api/v1/bookings/{id}/cancel', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);
    $booking = makePassengerBookingForCancelTests($passenger);

    $this->postJson("/api/v1/bookings/{$booking->id}/cancel", [
        'reason_code' => 'CHANGED_MIND',
    ])->assertUnauthorized();
});

it('forbids cancel without booking:cancel:self ability', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create']);

    $booking = makePassengerBookingForCancelTests($passenger);

    $this->postJson("/api/v1/bookings/{$booking->id}/cancel", [
        'reason_code' => 'CHANGED_MIND',
    ])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('cancels a searching-driver booking for the owning passenger', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:cancel:self']);

    $create = $this->postJson('/api/v1/bookings', sampleBookingPayload());
    $create->assertOk();
    $bookingId = $create->json('data.booking.id');

    $cancel = $this->postJson("/api/v1/bookings/{$bookingId}/cancel", [
        'reason_code' => 'CHANGED_MIND',
        'notes' => 'No longer needed',
    ]);

    $cancel->assertOk();
    $cancel->assertJsonPath('success', true);
    $cancel->assertJsonPath('data.status', Booking::STATUS_CANCELLED_BY_PASSENGER);
    $cancel->assertJsonPath('data.booking_id', $bookingId);

    $booking = Booking::query()->findOrFail($bookingId);
    expect($booking->status)->toBe(Booking::STATUS_CANCELLED_BY_PASSENGER)
        ->and($booking->cancelled_at)->not->toBeNull()
        ->and($booking->cancellation_reason)->toContain('CHANGED_MIND');
});

it('returns 403 when another passenger attempts cancel', function () {
    $owner = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);
    $other = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($owner, ['booking:create', 'booking:cancel:self']);
    $bookingId = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');

    Sanctum::actingAs($other, ['booking:create', 'booking:cancel:self']);

    $this->postJson("/api/v1/bookings/{$bookingId}/cancel", [
        'reason_code' => 'CHANGED_MIND',
    ])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('allows cancel within grace period after driver assignment', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:cancel:self']);

    $bookingId = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');
    $booking = Booking::query()->findOrFail($bookingId);
    $booking->status = Booking::STATUS_DRIVER_ASSIGNED;
    $booking->accepted_at = Carbon::now()->subSeconds(30);
    $booking->save();

    $this->postJson("/api/v1/bookings/{$bookingId}/cancel", [
        'reason_code' => 'CHANGED_MIND',
    ])
        ->assertOk()
        ->assertJsonPath('data.status', Booking::STATUS_CANCELLED_BY_PASSENGER);
});

it('rejects cancel after grace period when driver was assigned', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:cancel:self']);

    $bookingId = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');
    $booking = Booking::query()->findOrFail($bookingId);
    $booking->status = Booking::STATUS_DRIVER_ASSIGNED;
    $booking->accepted_at = Carbon::now()->subMinutes(5);
    $booking->save();

    $this->postJson("/api/v1/bookings/{$bookingId}/cancel", [
        'reason_code' => 'CHANGED_MIND',
    ])
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'BOOKING_NOT_CANCELLABLE');
});

it('requires authentication for GET /api/v1/bookings', function () {
    $this->getJson('/api/v1/bookings')->assertUnauthorized();
});

it('requires booking:read:self for GET /api/v1/bookings', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create']);

    $this->getJson('/api/v1/bookings')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('lists bookings for the authenticated passenger', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:read:self']);

    $id = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');

    $list = $this->getJson('/api/v1/bookings');
    $list->assertOk();
    $list->assertJsonPath('success', true);
    $list->assertJsonPath('data.bookings.0.id', $id);
});

it('filters active bookings when active=1', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:read:self']);

    $activeId = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');
    $booking = Booking::query()->findOrFail($activeId);
    $booking->status = Booking::STATUS_COMPLETED;
    $booking->save();

    $this->postJson('/api/v1/bookings', sampleBookingPayload())->assertOk();

    $resp = $this->getJson('/api/v1/bookings?active=1');
    $resp->assertOk();
    expect($resp->json('data.bookings'))->toHaveCount(1)
        ->and($resp->json('data.bookings.0.id'))->not->toBe($activeId);
});

it('returns a single booking for GET /api/v1/bookings/{booking}', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:read:self']);

    $id = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');

    $this->getJson("/api/v1/bookings/{$id}")
        ->assertOk()
        ->assertJsonPath('data.booking.id', $id)
        ->assertJsonPath('data.booking.status', Booking::STATUS_SEARCHING_DRIVER);
});

it('returns 403 when passenger reads another users booking', function () {
    $owner = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);
    $other = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($owner, ['booking:create']);
    $id = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');

    Sanctum::actingAs($other, ['booking:read:self']);

    $this->getJson("/api/v1/bookings/{$id}")
        ->assertForbidden()
        ->assertJsonPath('error.code', 'FORBIDDEN');
});

it('cancels open dispatch attempts when passenger cancels during search', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:cancel:self']);

    $bookingId = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');

    $attempt = BookingDispatchAttempt::query()->where('booking_id', $bookingId)->first();
    expect($attempt)->not->toBeNull()
        ->and($attempt->status)->toBe('OPEN');

    $this->postJson("/api/v1/bookings/{$bookingId}/cancel", [
        'reason_code' => 'CHANGED_MIND',
    ])->assertOk();

    expect($attempt->fresh()->status)->toBe('CANCELLED');
});

it('is idempotent when booking already cancelled by passenger', function () {
    $passenger = User::factory()->create([
        'role' => 'passenger',
        'status' => 'ACTIVE',
    ]);

    Sanctum::actingAs($passenger, ['booking:create', 'booking:cancel:self']);

    $bookingId = $this->postJson('/api/v1/bookings', sampleBookingPayload())->json('data.booking.id');

    $this->postJson("/api/v1/bookings/{$bookingId}/cancel", ['reason_code' => 'CHANGED_MIND'])->assertOk();
    $again = $this->postJson("/api/v1/bookings/{$bookingId}/cancel", ['reason_code' => 'CHANGED_MIND']);

    $again->assertOk();
    $again->assertJsonPath('data.status', Booking::STATUS_CANCELLED_BY_PASSENGER);
});
