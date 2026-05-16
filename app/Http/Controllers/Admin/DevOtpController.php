<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtpChallenge;
use App\Services\Otp\OtpChallengeService;
use Illuminate\Support\Facades\Cache;

/**
 * PRD §5 — pilot has no SMS provider yet, so admins read fresh OTP codes from this
 * dev-only page during demos. The route is gated by APP_DEBUG so production deploys
 * cannot leak codes even if it were accidentally exposed.
 */
class DevOtpController extends Controller
{
    public function index()
    {
        abort_unless(config('app.debug') === true, 404);

        $challenges = OtpChallenge::query()
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id', 'phone_number', 'role_hint', 'expires_at', 'consumed_at', 'locked_at', 'verify_attempts', 'created_at']);

        $rows = $challenges->map(function (OtpChallenge $c) {
            $plaintext = Cache::get(OtpChallengeService::devPlaintextCacheKey($c->id));

            return [
                'id' => $c->id,
                'phone_number' => $c->phone_number,
                'role_hint' => $c->role_hint,
                'plaintext' => is_string($plaintext) ? $plaintext : null,
                'verify_attempts' => $c->verify_attempts,
                'expires_at' => $c->expires_at,
                'consumed_at' => $c->consumed_at,
                'locked_at' => $c->locked_at,
                'created_at' => $c->created_at,
            ];
        });

        return view('admin.dev.otp', [
            'rows' => $rows,
            'cache_ttl_seconds' => OtpChallengeService::DEV_PLAINTEXT_CACHE_TTL_SECONDS,
        ]);
    }
}
