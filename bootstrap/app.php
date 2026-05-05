<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Allow reverse proxies (e.g. ngrok / nginx) to convey the original
        // scheme/host via X-Forwarded-* headers so URL generation + redirects
        // behave correctly under HTTPS tunnels.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'idempotent' => \App\Http\Middleware\IdempotencyKey::class,
            'driver.booking.cancel' => \App\Http\Middleware\EnsureDriverCanCancelBooking::class,
            'driver.profile.read' => \App\Http\Middleware\EnsureDriverCanReadProfile::class,
            'passenger.booking' => \App\Http\Middleware\EnsurePassengerCanCreateBooking::class,
            'passenger.booking.cancel' => \App\Http\Middleware\EnsurePassengerCanCancelBooking::class,
            'passenger.booking.read' => \App\Http\Middleware\EnsurePassengerCanReadBooking::class,
            'driver.dispatch.offers' => \App\Http\Middleware\EnsureDriverCanReadDispatchOffers::class,
            'driver.booking.accept' => \App\Http\Middleware\EnsureDriverCanAcceptBooking::class,
            'driver.booking.decline' => \App\Http\Middleware\EnsureDriverCanDeclineBooking::class,
            'driver.availability' => \App\Http\Middleware\EnsureDriverCanUpdateAvailability::class,
            'driver.booking.read' => \App\Http\Middleware\EnsureDriverCanReadAssignedBookings::class,
            'driver.trip.update' => \App\Http\Middleware\EnsureDriverCanUpdateTrip::class,
            'driver.trip.start' => \App\Http\Middleware\EnsureDriverCanStartTrip::class,
            'driver.trip.end' => \App\Http\Middleware\EnsureDriverCanEndTrip::class,
            'driver.trip.passengers' => \App\Http\Middleware\EnsureDriverCanAddTripPassengers::class,
            'driver.payment.record' => \App\Http\Middleware\EnsureDriverCanRecordPayment::class,
            'passenger.trip.read' => \App\Http\Middleware\EnsurePassengerCanReadTrip::class,
            'passenger.receipt.read' => \App\Http\Middleware\EnsurePassengerCanReadReceipt::class,
            'passenger.sos' => \App\Http\Middleware\EnsurePassengerCanCreateSos::class,
            'passenger.verified' => \App\Http\Middleware\EnsurePassengerAccountVerified::class,
            'lgu.only'           => \App\Http\Middleware\LguAdminOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $first = collect($e->errors())->flatten()->first();

                return ApiResponse::error(
                    'VALIDATION_ERROR',
                    is_string($first) ? $first : 'Validation failed.',
                    422,
                    $e->errors()
                );
            }

            return null;
        });
    })->create();
