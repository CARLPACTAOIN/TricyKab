<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePassengerCanCreateSos
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isPassenger()) {
            return ApiResponse::error('FORBIDDEN', 'Only passengers can create SOS alerts.', 403);
        }

        if (! $user->tokenCan('sos:create:self')) {
            return ApiResponse::error('FORBIDDEN', 'Missing sos:create:self permission.', 403);
        }

        return $next($request);
    }
}
