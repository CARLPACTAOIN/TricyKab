<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePassengerCanReadBooking
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isPassenger()) {
            return ApiResponse::error('FORBIDDEN', 'Only passengers can read passenger bookings.', 403);
        }

        if (! $user->tokenCan('booking:read:self')) {
            return ApiResponse::error('FORBIDDEN', 'Missing booking:read:self permission.', 403);
        }

        return $next($request);
    }
}
