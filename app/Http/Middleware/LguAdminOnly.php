<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LguAdminOnly
{
    /**
     * Handle an incoming request.
     * Deny access to TODA Admins — LGU/TMU municipal admin accounts only (PRD §17.1).
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->isMunicipalAdmin(),
            403,
            'Access denied. This area is restricted to LGU/TMU administrators only.'
        );

        return $next($request);
    }
}
