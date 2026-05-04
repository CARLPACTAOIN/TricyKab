<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDriverCanUpdateAvailability
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isDriver()) {
            return ApiResponse::error('FORBIDDEN', 'Only drivers can update availability.', 403);
        }

        if (! $user->tokenCan('availability:update:self')) {
            return ApiResponse::error('FORBIDDEN', 'Missing availability:update:self permission.', 403);
        }

        if ($user->driverProfile === null) {
            return ApiResponse::error('FORBIDDEN', 'Driver profile is not linked to this account.', 403);
        }

        if (strtolower((string) $user->driverProfile->status) !== 'active') {
            return ApiResponse::error('FORBIDDEN', 'Driver account is not active.', 403);
        }

        return $next($request);
    }
}
