<?php

use App\Models\Driver;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

it('requires authentication for POST /api/v1/drivers/me/availability', function () {
    $this->postJson('/api/v1/drivers/me/availability', [
        'driver_status' => 'ONLINE',
        'latitude' => 7.1,
        'longitude' => 124.8,
    ])->assertUnauthorized();
});

it('updates driver availability for an active driver token', function () {
    $this->seed(DatabaseSeeder::class);

    $driverUser = User::query()->where('email', 'driver_0@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, ['availability:update:self']);

    $response = $this->postJson('/api/v1/drivers/me/availability', [
        'driver_status' => 'ONLINE',
        'latitude' => 7.1083,
        'longitude' => 124.8295,
        'accuracy_meters' => 12.5,
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.driver_status', 'ONLINE');
    $response->assertJsonStructure([
        'data' => [
            'driver_id',
            'driver_status',
            'effective_at',
        ],
    ]);

    $driver = Driver::query()->where('user_id', $driverUser->id)->firstOrFail();
    expect($driver->availability_status)->toBe('ONLINE')
        ->and((float) $driver->last_latitude)->toBe(7.1083);
});

it('sets offline without coordinates', function () {
    $this->seed(DatabaseSeeder::class);

    $driverUser = User::query()->where('email', 'driver_1@tricykab.local')->firstOrFail();
    Sanctum::actingAs($driverUser, ['availability:update:self']);

    $this->postJson('/api/v1/drivers/me/availability', [
        'driver_status' => 'OFFLINE',
    ])
        ->assertOk()
        ->assertJsonPath('data.driver_status', 'OFFLINE');

    expect(Driver::query()->where('user_id', $driverUser->id)->first()?->availability_status)->toBe('OFFLINE');
});
