<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * PRD §5 / §8.5 / §22 — Idempotency-Key middleware.
 *
 * Replays the first response for any (user_id|key|method|path) tuple within the retention window
 * so that flaky-network retries cannot create duplicate bookings, accepts, payments, etc.
 */
class IdempotencyKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $rawKey = trim((string) $request->header('Idempotency-Key', ''));
        if ($rawKey === '') {
            return $next($request);
        }

        if (strlen($rawKey) > 200) {
            return ApiResponse::error(
                'VALIDATION_ERROR',
                'Idempotency-Key must be 200 characters or fewer.',
                422,
            );
        }

        $userId = optional($request->user())->id;
        $method = strtoupper((string) $request->method());
        $path = '/'.ltrim($request->path(), '/');
        $hash = hash('sha256', ($userId ?? 'anon').'|'.$method.'|'.$path.'|'.$rawKey);

        $existing = DB::table('idempotency_records')->where('key_hash', $hash)->first();
        if ($existing !== null) {
            $decoded = json_decode((string) $existing->response_body, true);
            if (! is_array($decoded)) {
                $decoded = ['success' => false, 'error' => ['code' => 'INTERNAL_ERROR', 'message' => 'Stored idempotent response was unreadable.']];
            }

            return response()->json($decoded, (int) $existing->response_status);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response->getStatusCode() < 500) {
            DB::table('idempotency_records')->insertOrIgnore([
                'key_hash' => $hash,
                'user_id' => $userId,
                'method' => $method,
                'path' => substr($path, 0, 250),
                'response_status' => $response->getStatusCode(),
                'response_body' => (string) $response->getContent(),
                'created_at' => now(),
            ]);
        }

        return $response;
    }
}
