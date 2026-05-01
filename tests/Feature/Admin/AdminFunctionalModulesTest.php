<?php

use App\Models\Driver;
use App\Models\Dispute;
use App\Models\SosAlert;
use App\Models\Tricycle;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('loads backend-driven admin modules', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $driver = Driver::query()->firstOrFail();

    $this->actingAs($admin)->get(route('admin.disputes'))
        ->assertOk()
        ->assertViewHas('disputes');

    $this->actingAs($admin)->get(route('admin.sos'))
        ->assertOk()
        ->assertViewHas('alerts');

    $this->actingAs($admin)->get(route('admin.analytics'))
        ->assertOk()
        ->assertViewHas('topDrivers')
        ->assertViewHas('flaggedDrivers');

    $this->actingAs($admin)->get(route('admin.audit-logs'))
        ->assertOk()
        ->assertViewHas('logs');

    $this->actingAs($admin)->get(route('drivers.show', $driver))
        ->assertOk()
        ->assertViewHas('metrics');
});

it('requires registration status and capacity for tricycle creation', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $todaId = \App\Models\Toda::query()->firstOrFail()->id;

    $response = $this->actingAs($admin)->post(route('tricycles.store'), [
        'body_number' => 'NEW-991',
        'plate_number' => 'PL-991',
        'toda_id' => $todaId,
        'make_model' => 'Test Model',
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors(['registration_status', 'capacity']);
});

it('stores tricycle with active lto and capacity', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $todaId = \App\Models\Toda::query()->firstOrFail()->id;

    $this->actingAs($admin)->post(route('tricycles.store'), [
        'body_number' => 'NEW-777',
        'plate_number' => 'PL-777',
        'toda_id' => $todaId,
        'make_model' => 'Test Model',
        'status' => 'active',
        'registration_status' => 'ACTIVE',
        'capacity' => 4,
    ])->assertRedirect(route('tricycles.index'));

    $this->assertDatabaseHas('tricycles', [
        'body_number' => 'NEW-777',
        'registration_status' => 'ACTIVE',
        'capacity' => 4,
    ]);
});

it('filters driver profile by range', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $driver = Driver::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('drivers.show', ['driver' => $driver->id, 'range' => 'month']))
        ->assertOk()
        ->assertSee('Driver profile');
});

it('supports bulk dispute and sos actions plus csv exports', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $disputeIds = Dispute::query()->take(2)->pluck('id')->all();
    $alertIds = SosAlert::query()->take(2)->pluck('id')->all();

    $this->actingAs($admin)->patch(route('admin.disputes.bulk-update'), [
        'dispute_ids' => $disputeIds,
        'status' => 'UNDER_REVIEW',
        'resolution_notes' => 'Bulk test',
    ])->assertRedirect(route('admin.disputes'));

    foreach ($disputeIds as $id) {
        $this->assertDatabaseHas('disputes', ['id' => $id, 'status' => 'UNDER_REVIEW']);
    }

    $this->actingAs($admin)->patch(route('admin.sos.bulk-update-status'), [
        'alert_ids' => $alertIds,
        'status' => 'ACKNOWLEDGED',
    ])->assertRedirect(route('admin.sos'));

    foreach ($alertIds as $id) {
        $this->assertDatabaseHas('sos_alerts', ['id' => $id, 'status' => 'ACKNOWLEDGED']);
    }

    $this->actingAs($admin)->get(route('admin.disputes.export'))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $this->actingAs($admin)->get(route('admin.sos.export'))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $this->actingAs($admin)->get(route('admin.audit-logs.export'))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
