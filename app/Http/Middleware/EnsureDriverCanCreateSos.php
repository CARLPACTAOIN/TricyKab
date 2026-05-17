<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDriverCanCreateSos
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isDriver()) {
            return ApiResponse::error('FORBIDDEN', 'Only drivers can create driver SOS alerts.', 403);
        }

        if (! $user->tokenCan('sos:create:self')) {
            return ApiResponse::error('FORBIDDEN', 'Missing sos:create:self permission.', 403);
        }

        return $next($request);
    }
}
