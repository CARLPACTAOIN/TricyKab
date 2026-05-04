<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePassengerCanCreateBooking
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isPassenger()) {
            return ApiResponse::error('FORBIDDEN', 'Only passengers can create bookings.', 403);
        }

        if (! $user->tokenCan('booking:create')) {
            return ApiResponse::error('FORBIDDEN', 'Missing booking:create permission.', 403);
        }

        return $next($request);
    }
}
