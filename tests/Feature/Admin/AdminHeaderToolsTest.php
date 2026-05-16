<?php

use App\Models\Dispute;
use App\Models\SosAlert;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('returns admin notifications from backend data', function () {
    $admin = User::query()->where('role', 'admin')->whereNull('toda_id')->firstOrFail();

    SosAlert::query()->where('status', 'OPEN')->delete();
    Dispute::query()->whereIn('status', ['OPEN', 'UNDER_REVIEW'])->delete();

    SosAlert::create([
        'passenger_name' => 'Test Passenger',
        'status' => 'OPEN',
        'latitude' => 7.123,
        'longitude' => 124.456,
    ]);

    Dispute::create([
        'reported_by_role' => 'passenger',
        'dispute_type' => 'FARE',
        'description' => 'Overcharged',
        'status' => 'OPEN',
    ]);

    $response = $this->actingAs($admin)->getJson(route('admin.notifications'));

    $response->assertOk()
        ->assertJsonPath('unread_count', 2)
        ->assertJsonCount(2, 'items');
});

it('dismisses a notification for the session', function () {
    $admin = User::query()->where('role', 'admin')->whereNull('toda_id')->firstOrFail();

    SosAlert::query()->where('status', 'OPEN')->delete();
    Dispute::query()->whereIn('status', ['OPEN', 'UNDER_REVIEW'])->delete();

    $alert = SosAlert::create([
        'passenger_name' => 'Dismiss Me',
        'status' => 'OPEN',
    ]);

    $this->actingAs($admin)
        ->postJson(route('admin.notifications.dismiss'), ['key' => 'sos:'.$alert->id])
        ->assertOk()
        ->assertJsonPath('unread_count', 0);

    $this->actingAs($admin)
        ->getJson(route('admin.notifications'))
        ->assertJsonPath('unread_count', 0);
});

it('returns global search suggestions', function () {
    $admin = User::query()->where('role', 'admin')->whereNull('toda_id')->firstOrFail();
    $driver = \App\Models\Driver::query()->firstOrFail();

    $response = $this->actingAs($admin)->getJson(route('admin.search.suggest', [
        'q' => substr($driver->first_name, 0, 3),
    ]));

    $response->assertOk()->assertJsonStructure(['groups']);
});

it('loads global search results page', function () {
    $admin = User::query()->where('role', 'admin')->whereNull('toda_id')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.search', ['q' => 'test']))
        ->assertOk()
        ->assertViewHas('bookings');
});
