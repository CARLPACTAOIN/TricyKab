<?php

use App\Http\Controllers\Api\V1\DriverAvailabilityController;
use App\Http\Controllers\Api\V1\DriverBookingController;
use App\Http\Controllers\Api\V1\DriverDispatchController;
use App\Http\Controllers\Api\V1\DriverProfileController;
use App\Http\Controllers\Api\V1\DriverTripController;
use App\Http\Controllers\Api\V1\OtpAuthController;
use App\Http\Controllers\Api\V1\PassengerAuthController;
use App\Http\Controllers\Api\V1\PassengerBookingController;
use App\Http\Controllers\Api\V1\PassengerProfileController;
use App\Http\Controllers\Api\V1\PassengerSosController;
use App\Http\Controllers\Api\V1\PassengerTripController;
use App\Http\Controllers\Api\V1\PaymentRecordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TricyKab (Kabacan Smart Tricycle Dispatch)
|--------------------------------------------------------------------------
|
| Mobile apps (passenger/driver) and integrations consume JSON under /api.
| Version prefixes keep breaking changes isolated (e.g. v1, v2).
|
| Authenticated passenger routes use `auth:sanctum` (see POST `/bookings`).
|
*/

Route::prefix('v1')->group(function (): void {
    Route::get('/ping', function () {
        return response()->json([
            'ok' => true,
            'service' => 'tricykab',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '0.0.0-dev'),
        ]);
    });

    Route::post('/auth/otp/request', [OtpAuthController::class, 'requestOtp']);
    Route::post('/auth/otp/verify', [OtpAuthController::class, 'verify']);

    Route::post('/passenger/register', [PassengerAuthController::class, 'register']);
    Route::post('/passenger/verify-phone', [PassengerAuthController::class, 'verifyPhone']);
    Route::post('/passenger/login', [PassengerAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'passenger.verified'])->group(function (): void {
        Route::get('/passenger/me/profile', [PassengerProfileController::class, 'show']);
        Route::post('/passenger/me/profile', [PassengerProfileController::class, 'upsert']);
    });

    Route::middleware(['auth:sanctum', 'passenger.booking', 'idempotent'])->post('/bookings', [PassengerBookingController::class, 'store']);

    Route::middleware(['auth:sanctum', 'passenger.booking.cancel', 'idempotent'])
        ->post('/bookings/{booking}/cancel', [PassengerBookingController::class, 'cancel']);

    Route::middleware(['auth:sanctum', 'passenger.booking.read'])->group(function (): void {
        Route::get('/bookings', [PassengerBookingController::class, 'index']);
        Route::get('/bookings/{booking}', [PassengerBookingController::class, 'show']);
    });

    Route::middleware(['auth:sanctum', 'driver.availability'])
        ->post('/drivers/me/availability', [DriverAvailabilityController::class, 'store']);

    Route::middleware(['auth:sanctum', 'driver.profile.read'])
        ->get('/drivers/me/profile', [DriverProfileController::class, 'show']);

    Route::middleware(['auth:sanctum', 'driver.booking.read'])->group(function (): void {
        Route::get('/drivers/me/bookings', [DriverBookingController::class, 'index']);
        Route::get('/drivers/me/bookings/{booking}', [DriverBookingController::class, 'show']);
    });

    Route::middleware(['auth:sanctum', 'driver.dispatch.offers'])
        ->get('/drivers/me/dispatch-offers', [DriverDispatchController::class, 'index']);

    Route::middleware(['auth:sanctum', 'driver.booking.accept', 'idempotent'])
        ->post('/drivers/bookings/{booking}/accept', [DriverDispatchController::class, 'accept']);

    Route::middleware(['auth:sanctum', 'driver.booking.decline'])
        ->post('/drivers/bookings/{booking}/decline', [DriverDispatchController::class, 'decline']);

    Route::middleware(['auth:sanctum', 'driver.booking.cancel', 'idempotent'])
        ->post('/drivers/bookings/{booking}/cancel', [DriverDispatchController::class, 'cancel']);

    Route::middleware(['auth:sanctum', 'driver.trip.update'])
        ->post('/drivers/trips/{trip}/arrive', [DriverTripController::class, 'arrive']);

    Route::middleware(['auth:sanctum', 'driver.trip.update'])
        ->post('/drivers/trips/{trip}/location', [DriverTripController::class, 'location']);

    Route::middleware(['auth:sanctum', 'driver.trip.start', 'idempotent'])
        ->post('/drivers/trips/{trip}/start', [DriverTripController::class, 'start']);

    Route::middleware(['auth:sanctum', 'driver.trip.passengers'])
        ->post('/drivers/trips/{trip}/add-passengers', [DriverTripController::class, 'addPassengers']);

    Route::middleware(['auth:sanctum', 'driver.trip.end', 'idempotent'])
        ->post('/drivers/trips/{trip}/end', [DriverTripController::class, 'end']);

    Route::middleware(['auth:sanctum', 'driver.payment.record', 'idempotent'])
        ->post('/payments/{booking}/record', [PaymentRecordController::class, 'store']);

    Route::middleware(['auth:sanctum', 'passenger.booking.read', 'passenger.trip.read'])
        ->get('/bookings/{booking}/trip-tracking', [PassengerTripController::class, 'tracking']);

    Route::middleware(['auth:sanctum', 'passenger.booking.read', 'passenger.trip.read', 'idempotent'])
        ->post('/bookings/{booking}/passenger-ack', [PassengerTripController::class, 'passengerAck']);

    Route::middleware(['auth:sanctum', 'passenger.booking.read', 'passenger.receipt.read'])
        ->get('/bookings/{booking}/receipt', [PassengerTripController::class, 'receipt']);

    Route::middleware(['auth:sanctum', 'passenger.sos'])
        ->post('/passenger/sos', [PassengerSosController::class, 'store']);
});
