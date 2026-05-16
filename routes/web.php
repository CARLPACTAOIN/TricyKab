<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DevOtpController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\SosAlertController;
use App\Http\Controllers\Admin\StandbyPointController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

// Public web-app surfaces (UI + client-side state; backend wiring pending)
Route::prefix('passenger-app')->group(function () {
    Route::get('/', function () {
        return view('webapp.passenger', ['activeNav' => 'book']);
    })->name('passenger.app');

    Route::get('/trips', function () {
        return view('webapp.passenger-trips', [
            'activeNav' => 'trips',
            'demoTrips' => [
                [
                    'ref' => 'BKG-2026-000042',
                    'status' => 'COMPLETED',
                    'pickup' => 'Kabacan Public Market',
                    'destination' => 'USM Main Gate',
                    'fare' => '35.00',
                    'rideType' => 'SHARED',
                    'at' => '2026-04-14T10:30:00+08:00',
                ],
                [
                    'ref' => 'BKG-2026-000018',
                    'status' => 'CANCELLED_BY_PASSENGER',
                    'pickup' => 'Poblacion',
                    'destination' => 'Hospital',
                    'fare' => '—',
                    'rideType' => 'SPECIAL',
                    'at' => '2026-04-12T16:05:00+08:00',
                ],
            ],
        ]);
    })->name('passenger.trips');

    Route::get('/profile', function () {
        return view('webapp.passenger-profile', ['activeNav' => 'profile']);
    })->name('passenger.profile');
});

Route::prefix('driver-app')->group(function () {
    Route::get('/', function () {
        return view('webapp.driver', ['activeNav' => 'dashboard']);
    })->name('driver.app');

    Route::get('/earnings', function () {
        return view('webapp.driver-earnings', ['activeNav' => 'earnings']);
    })->name('driver.earnings');

    Route::get('/account', function () {
        return view('webapp.driver-account', ['activeNav' => 'account']);
    })->name('driver.account');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

// Profile Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard-export', [DashboardController::class, 'export'])->name('admin.dashboard.export');

    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('admin.search');
    Route::get('/search/suggest', [\App\Http\Controllers\Admin\SearchController::class, 'suggest'])->name('admin.search.suggest');
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications');
    Route::post('/notifications/dismiss', [\App\Http\Controllers\Admin\NotificationController::class, 'dismiss'])->name('admin.notifications.dismiss');

    // Shared Routes (accessible to all admin roles; TODA scoping enforced in controllers)
    Route::resource('tricycles', \App\Http\Controllers\Admin\TricycleController::class);
    Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class);

    // Bookings & Trips
    Route::get('bookings', [BookingController::class, 'index'])->name('admin.bookings');
    Route::get('bookings-export', [BookingController::class, 'export'])->name('admin.bookings.export');
    Route::get('bookings/{reference}', [BookingController::class, 'show'])->name('admin.bookings.show');

    // PRD §6.5 / §7.19 / §9.5 — AJAX endpoints for booking detail page modals
    Route::post('bookings/{reference}/override', [BookingController::class, 'override'])->name('admin.bookings.override');
    Route::get('bookings/{reference}/receipt-data', [BookingController::class, 'receiptData'])->name('admin.bookings.receipt-data');
    Route::post('bookings/{reference}/dispute', [BookingController::class, 'openDispute'])->name('admin.bookings.dispute');

    Route::get('analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');

    // LGU-Only Routes
    Route::middleware('lgu.only')->group(function () {
        // Fleet Management (TODAs)
        Route::resource('todas', \App\Http\Controllers\Admin\TodaController::class);

        // Fare Management
        Route::get('fares', [\App\Http\Controllers\Admin\FareController::class, 'index'])->name('fares.index');
        Route::post('fares', [\App\Http\Controllers\Admin\FareController::class, 'store'])->name('fares.store');

        // Standby Points
        Route::get('standby-points', [StandbyPointController::class, 'index'])->name('admin.standby-points');
        Route::get('standby-points/create', [StandbyPointController::class, 'create'])->name('admin.standby-points.create');
        Route::post('standby-points', [StandbyPointController::class, 'store'])->name('admin.standby-points.store');
        Route::get('standby-points/{standbyPoint}/edit', [StandbyPointController::class, 'edit'])->name('admin.standby-points.edit');
        Route::put('standby-points/{standbyPoint}', [StandbyPointController::class, 'update'])->name('admin.standby-points.update');
        Route::delete('standby-points/{standbyPoint}', [StandbyPointController::class, 'destroy'])->name('admin.standby-points.destroy');

        // Disputes
        Route::get('disputes', [DisputeController::class, 'index'])->name('admin.disputes');
        Route::patch('disputes/{dispute}', [DisputeController::class, 'update'])->name('admin.disputes.update');
        Route::patch('disputes', [DisputeController::class, 'bulkUpdate'])->name('admin.disputes.bulk-update');
        Route::get('disputes-export', [DisputeController::class, 'export'])->name('admin.disputes.export');

        // SOS Alerts
        Route::get('sos-alerts', [SosAlertController::class, 'index'])->name('admin.sos');
        Route::get('sos-alerts/poll', [SosAlertController::class, 'poll'])->name('admin.sos.poll');
        Route::patch('sos-alerts/{sosAlert}/status', [SosAlertController::class, 'updateStatus'])->name('admin.sos.update-status');
        Route::patch('sos-alerts/status', [SosAlertController::class, 'bulkUpdateStatus'])->name('admin.sos.bulk-update-status');
        Route::get('sos-alerts-export', [SosAlertController::class, 'export'])->name('admin.sos.export');

        // Audit Logs
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs');
        Route::get('audit-logs-export', [AuditLogController::class, 'export'])->name('admin.audit-logs.export');
    });

    // Dev tools (visible only when APP_DEBUG is true)
    Route::get('dev/otp', [DevOtpController::class, 'index'])->name('admin.dev.otp');
});

require __DIR__.'/auth.php';

// Mockups routing (static HTML/CSS/JS under /mockups)
Route::get('/mockups/{path?}', function (?string $path = null) {
    $base = base_path('mockups');

    $path = $path ?: 'index.html';
    $path = ltrim($path, '/');

    // Prevent path traversal
    if (Str::contains($path, ['..', '\\'])) {
        abort(404);
    }

    $full = $base.DIRECTORY_SEPARATOR.$path;

    // Support directory paths by serving index.html
    if (is_dir($full)) {
        $full = rtrim($full, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'index.html';
    }

    if (! is_file($full)) {
        abort(404);
    }

    $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
    $allowed = ['html', 'css', 'js', 'png', 'jpg', 'jpeg', 'svg', 'webp', 'gif', 'ico'];
    if (! in_array($ext, $allowed, true)) {
        abort(404);
    }

    $contentTypes = [
        'html' => 'text/html; charset=UTF-8',
        'css' => 'text/css; charset=UTF-8',
        'js' => 'text/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
    ];

    return response()->file($full, [
        'Content-Type' => $contentTypes[$ext] ?? 'application/octet-stream',
        'Cache-Control' => 'no-store',
    ]);
})->where('path', '.*');
