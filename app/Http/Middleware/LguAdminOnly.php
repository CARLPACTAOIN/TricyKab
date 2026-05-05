<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LguAdminOnly
{
    /**
     * Handle an incoming request.
     * Deny access to TODA Admins — LGU Admin accounts only.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->isLguAdmin(),
            403,
            'Access denied. This area is restricted to LGU Administrators only.'
        );

        return $next($request);
    }
}
