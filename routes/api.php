<?php

use App\Http\Controllers\Api\V1\OtpAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TricyKab (Kabacan Smart Tricycle Dispatch)
|--------------------------------------------------------------------------
|
| Mobile apps (passenger/driver) and integrations consume JSON under /api.
| Version prefixes keep breaking changes isolated (e.g. v1, v2).
|
| Future authenticated routes (OTP, Sanctum tokens):
|   Route::prefix('v1')->middleware('auth:sanctum')->group(function () { ... });
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
});
