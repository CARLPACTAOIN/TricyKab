<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

// Profile Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // This should point to the admin dashboard
    })->name('admin.dashboard');

    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('admin.search');

    Route::get('shell-proof', function () {
        return view('admin.shell-proof');
    })->name('admin.shell-proof');

    // Fleet Management
    Route::resource('todas', \App\Http\Controllers\Admin\TodaController::class);
    Route::resource('tricycles', \App\Http\Controllers\Admin\TricycleController::class);
    Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class);
    
    // Fare Management
    Route::get('fares', [\App\Http\Controllers\Admin\FareController::class, 'index'])->name('fares.index');
    Route::post('fares', [\App\Http\Controllers\Admin\FareController::class, 'store'])->name('fares.store');
    
    // PRD-aligned placeholders for upcoming admin modules
    Route::get('bookings', function () {
        return view('admin.coming-soon', [
            'title' => 'Bookings & Trips',
            'description' => 'Operational booking visibility, filters, and manual overrides will live here.',
        ]);
    })->name('admin.bookings');

    Route::get('standby-points', function () {
        return view('admin.coming-soon', [
            'title' => 'Standby Points',
            'description' => 'LGU/TODA approved standby points and geofences management.',
        ]);
    })->name('admin.standby-points');

    Route::get('disputes', function () {
        return view('admin.coming-soon', [
            'title' => 'Disputes',
            'description' => 'Fare disputes and trip incident resolution workspace.',
        ]);
    })->name('admin.disputes');

    Route::get('sos-alerts', function () {
        return view('admin.coming-soon', [
            'title' => 'SOS Alerts',
            'description' => 'Active SOS alerts, acknowledgement, and escalation history.',
        ]);
    })->name('admin.sos');

    Route::get('analytics', function () {
        return view('admin.coming-soon', [
            'title' => 'Analytics',
            'description' => 'KPI dashboards, heatmaps, and exports aligned with PRD reporting.',
        ]);
    })->name('admin.analytics');

    Route::get('audit-logs', function () {
        return view('admin.coming-soon', [
            'title' => 'Audit Logs',
            'description' => 'Immutable admin actions log with filters and exports.',
        ]);
    })->name('admin.audit-logs');
});

// Mockup Routes
Route::prefix('mockups/passenger')->group(function () {
    Route::get('/book-ride', function () {
        return view('mockups.passenger.book-ride');
    })->name('mockups.passenger.book-ride');

    Route::get('/assigned-driver', function () {
        return view('mockups.passenger.assigned-driver');
    })->name('mockups.passenger.assigned-driver');

    Route::get('/trip-in-progress', function () {
        return view('mockups.passenger.trip-in-progress');
    })->name('mockups.passenger.trip-in-progress');
});

require __DIR__.'/auth.php';
