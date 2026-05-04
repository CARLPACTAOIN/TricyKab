<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePassengerCanCancelBooking
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isPassenger()) {
            return ApiResponse::error('FORBIDDEN', 'Only passengers can cancel bookings.', 403);
        }

        if (! $user->tokenCan('booking:cancel:self')) {
            return ApiResponse::error('FORBIDDEN', 'Missing booking:cancel:self permission.', 403);
        }

        return $next($request);
    }
}
