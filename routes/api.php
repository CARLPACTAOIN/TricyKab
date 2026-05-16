<?php

use App\Http\Controllers\Api\V1\DisputeController;
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
use App\Http\Controllers\Api\V1\TripRatingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TricyKab (Kabacan Smart Tricycle Dispatch)
|--------------------------------------------------------------------------
|
| Mobile apps (passenger/driver) and integrations consume JSON under /api.
| Version prefixes keep breaking changes isolated (e.g. v1, v2).
|
| Rate limiting applied per-bucket per PRD §5.3:
|   - otp-request: 5/hour per phone
|   - otp-verify:  10/hour per phone
|   - api:         120/minute per authenticated user
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

    // PRD §5.3 — Public OTP & registration endpoints, rate-limited by phone
    Route::middleware('throttle:otp-request')->group(function (): void {
        Route::post('/auth/otp/request', [OtpAuthController::class, 'requestOtp']);
        Route::post('/passenger/register', [PassengerAuthController::class, 'register']);
    });

    Route::middleware('throttle:otp-verify')->group(function (): void {
        Route::post('/auth/otp/verify', [OtpAuthController::class, 'verify']);
        Route::post('/passenger/login', [PassengerAuthController::class, 'login']);
        Route::post('/passenger/verify-phone', [PassengerAuthController::class, 'verifyPhone']);
    });

    // All authenticated operational routes — 120 req/min per user
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {

        // Passenger profile (PRD §9.1 / §15)
        Route::middleware('passenger.verified')->group(function (): void {
            Route::get('/passenger/me/profile', [PassengerProfileController::class, 'show']);
            Route::post('/passenger/me/profile', [PassengerProfileController::class, 'upsert']);
        });

        // Passenger bookings (PRD §9.2)
        Route::middleware(['passenger.booking', 'idempotent'])
            ->post('/bookings', [PassengerBookingController::class, 'store']);
        Route::middleware(['passenger.booking.cancel', 'idempotent'])
            ->post('/bookings/{booking}/cancel', [PassengerBookingController::class, 'cancel']);
        Route::middleware('passenger.booking.read')->group(function (): void {
            Route::get('/bookings', [PassengerBookingController::class, 'index']);
            Route::get('/bookings/{booking}', [PassengerBookingController::class, 'show']);
        });

        // Passenger trip tracking + receipt (PRD §9.5)
        Route::middleware(['passenger.booking.read', 'passenger.trip.read'])->group(function (): void {
            Route::get('/bookings/{booking}/trip-tracking', [PassengerTripController::class, 'tracking']);
            Route::middleware('idempotent')
                ->post('/bookings/{booking}/passenger-ack', [PassengerTripController::class, 'passengerAck']);
        });
        Route::middleware(['passenger.booking.read', 'passenger.receipt.read'])
            ->get('/bookings/{booking}/receipt', [PassengerTripController::class, 'receipt']);

        // PRD §3.1 — Passenger rates driver after trip completion
        Route::middleware(['passenger.trip.read', 'idempotent'])
            ->post('/trips/{trip}/rate', [TripRatingController::class, 'store']);

        // PRD §7.19 — Dispute filing (passenger or driver on own booking)
        Route::middleware('idempotent')
            ->post('/bookings/{booking}/dispute', [DisputeController::class, 'store']);

        // Passenger SOS (PRD §3.1)
        Route::middleware('passenger.sos')
            ->post('/passenger/sos', [PassengerSosController::class, 'store']);

        // Driver availability + profile (PRD §14.1)
        Route::middleware('driver.availability')
            ->post('/drivers/me/availability', [DriverAvailabilityController::class, 'store']);
        Route::middleware('driver.profile.read')
            ->get('/drivers/me/profile', [DriverProfileController::class, 'show']);

        // Driver booking history (PRD §16)
        Route::middleware('driver.booking.read')->group(function (): void {
            Route::get('/drivers/me/bookings', [DriverBookingController::class, 'index']);
            Route::get('/drivers/me/bookings/{booking}', [DriverBookingController::class, 'show']);
        });

        // Dispatch offers (PRD §12)
        Route::middleware('driver.dispatch.offers')
            ->get('/drivers/me/dispatch-offers', [DriverDispatchController::class, 'index']);
        Route::middleware(['driver.booking.accept', 'idempotent'])
            ->post('/drivers/bookings/{booking}/accept', [DriverDispatchController::class, 'accept']);
        Route::middleware('driver.booking.decline')
            ->post('/drivers/bookings/{booking}/decline', [DriverDispatchController::class, 'decline']);
        Route::middleware(['driver.booking.cancel', 'idempotent'])
            ->post('/drivers/bookings/{booking}/cancel', [DriverDispatchController::class, 'cancel']);

        // Trip lifecycle (PRD §9.5 / §11)
        Route::middleware('driver.trip.update')->group(function (): void {
            Route::post('/drivers/trips/{trip}/arrive', [DriverTripController::class, 'arrive']);
            Route::post('/drivers/trips/{trip}/location', [DriverTripController::class, 'location']);
        });
        Route::middleware(['driver.trip.start', 'idempotent'])
            ->post('/drivers/trips/{trip}/start', [DriverTripController::class, 'start']);
        Route::middleware('driver.trip.passengers')
            ->post('/drivers/trips/{trip}/add-passengers', [DriverTripController::class, 'addPassengers']);
        Route::middleware(['driver.trip.end', 'idempotent'])
            ->post('/drivers/trips/{trip}/end', [DriverTripController::class, 'end']);

        // Payment recording (PRD §9.5)
        Route::middleware(['driver.payment.record', 'idempotent'])
            ->post('/payments/{booking}/record', [PaymentRecordController::class, 'store']);
    });
});
