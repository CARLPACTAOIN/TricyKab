<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // This should point to the admin dashboard
    })->name('admin.dashboard');

    // Fleet Management
    Route::resource('todas', \App\Http\Controllers\Admin\TodaController::class);
    Route::resource('tricycles', \App\Http\Controllers\Admin\TricycleController::class);
    Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class);
    
    // Fare Management
    Route::get('fares', [\App\Http\Controllers\Admin\FareController::class, 'index'])->name('fares.index');
    Route::post('fares', [\App\Http\Controllers\Admin\FareController::class, 'store'])->name('fares.store');
    
    // Test Route for Fare Calculation (Temporary)
    Route::get('test-fare', function (\App\Services\FareCalculatorService $service) {
        return [
            'regular_5km' => $service->calculate(5),
            'discounted_5km' => $service->calculate(5, true),
            'special_5km' => $service->calculateSpecial(5),
            'regular_2km' => $service->calculate(2), // Minimum distance check
        ];
    });
});

require __DIR__.'/auth.php';
