<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('allows admins to load the map-backed admin pages', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $booking = Booking::query()->firstOrFail();

    $dashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
    $dashboardResponse
        ->assertOk()
        ->assertViewHas('pickupHeatmapPoints', fn (array $points) => collect($points)->every(
            fn (array $point) => isset($point['lat'], $point['lng'])
        ))
        ->assertViewHas('destinationHeatmapPoints', fn (array $points) => collect($points)->every(
            fn (array $point) => isset($point['lat'], $point['lng'])
        ));

    $bookingsIndexResponse = $this->actingAs($admin)->get(route('admin.bookings'));
    $bookingsIndexResponse
        ->assertOk()
        ->assertViewHas('bookings');

    $bookingShowResponse = $this->actingAs($admin)->get(route('admin.bookings.show', $booking->booking_reference));
    $bookingShowResponse
        ->assertOk()
        ->assertViewHas('routeMapPayload', fn (array $payload) => isset(
            $payload['pickup']['lat'],
            $payload['pickup']['lng'],
            $payload['destination']['lat'],
            $payload['destination']['lng']
        ));

    $standbyPointsResponse = $this->actingAs($admin)->get(route('admin.standby-points'));
    $standbyPointsResponse
        ->assertOk()
        ->assertViewHas('standbyPoints', fn ($points) => $points->isNotEmpty());
});

it('blocks non-admin users from the admin map pages', function () {
    $passenger = User::query()->where('role', 'passenger')->firstOrFail();
    $booking = Booking::query()->firstOrFail();

    $this->actingAs($passenger)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($passenger)->get(route('admin.bookings'))->assertForbidden();
    $this->actingAs($passenger)->get(route('admin.bookings.show', $booking->booking_reference))->assertForbidden();
    $this->actingAs($passenger)->get(route('admin.standby-points'))->assertForbidden();
});

it('generates unique booking references for seeded bookings', function () {
    $references = Booking::query()->pluck('booking_reference');

    expect($references)->toHaveCount(Booking::query()->count());
    expect($references->filter()->count())->toBe(Booking::query()->count());
    expect($references->unique()->count())->toBe(Booking::query()->count());
    expect($references->first())->toStartWith('BK-');
});

it('filters standby points from the database', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();

    $response = $this->actingAs($admin)->get(route('admin.standby-points', [
        'status' => 'INACTIVE',
        'search' => 'Sangsang',
    ]));

    $response
        ->assertOk()
        ->assertViewHas('standbyPoints', fn ($points) => $points->count() === 1
            && $points->first()->name === 'Sangsang Waiting Shed'
            && $points->first()->status === 'INACTIVE');
});

it('resolves booking detail pages by booking reference', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $booking = Booking::query()->firstOrFail();

    $response = $this->actingAs($admin)->get(route('admin.bookings.show', $booking->booking_reference));

    $response
        ->assertOk()
        ->assertSee($booking->booking_reference)
        ->assertViewHas('booking', fn (Booking $viewBooking) => $viewBooking->is($booking));
});
