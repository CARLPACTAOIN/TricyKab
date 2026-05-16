<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('loads dashboard with prd kpi sections for lgu admin', function () {
    $admin = User::query()->where('email', 'admin@tricykab.test')->firstOrFail();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Trips per Barangay');
    $response->assertSee('LGU Admin');
    $response->assertSee('Export PDF');
});

it('exports dashboard csv and pdf', function () {
    $admin = User::query()->where('email', 'admin@tricykab.test')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.dashboard.export', ['range' => '7d']))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $this->actingAs($admin)
        ->get(route('admin.dashboard.export-pdf', ['range' => '7d']))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('scopes dashboard toda filter for toda admin', function () {
    $todaAdmin = User::query()
        ->where('role', 'admin')
        ->whereNotNull('toda_id')
        ->first();

    if ($todaAdmin === null) {
        $todaAdmin = User::factory()->create([
            'role' => 'admin',
            'toda_id' => 1,
            'admin_scope' => null,
            'email' => 'toda-admin@test.local',
            'password' => bcrypt('password'),
        ]);
    }

    $response = $this->actingAs($todaAdmin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('TODA Admin');
    $response->assertDontSee('name="toda_id"', false);
});

it('allows tmu admin to access lgu-only standby routes', function () {
    $tmu = User::query()->where('email', 'tmu@tricykab.test')->firstOrFail();

    $this->actingAs($tmu)
        ->get(route('admin.standby-points'))
        ->assertOk();
});

it('exports bookings pdf', function () {
    $admin = User::query()->where('email', 'admin@tricykab.test')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.bookings.export-pdf'))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
