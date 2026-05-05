<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePassengerAccountVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isPassenger()) {
            return ApiResponse::error('FORBIDDEN', 'Only passengers can access this resource.', 403);
        }

        if ($user->phone_verified_at === null) {
            return ApiResponse::error('FORBIDDEN', 'Phone number is not verified.', 403);
        }

        return $next($request);
    }
}

