<?php

namespace App\Services\Otp;

use App\Contracts\OtpSmsSender;
use App\Exceptions\Otp\DriverAccountMissingException;
use App\Exceptions\Otp\OperationForbiddenException;
use App\Exceptions\Otp\OtpChallengeLockedException;
use App\Exceptions\Otp\OtpHourlyRateLimitedException;
use App\Exceptions\Otp\OtpSendCooldownException;
use App\Models\Driver;
use App\Models\OtpChallenge;
use App\Models\User;
use App\Services\Auth\TokenIssuer;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpChallengeService
{
    private const OTP_EXPIRY_SECONDS = 300;

    private const HOURLY_DECAY_SECONDS = 3600;

    private const SEND_COOLDOWN_SECONDS = 60;

    private const MAX_VERIFY_ATTEMPTS = 5;

    public const DEV_PLAINTEXT_CACHE_TTL_SECONDS = 300;

    private const PASSENGER_ROLE_HINT = 'PASSENGER';

    private const DRIVER_ROLE_HINT = 'DRIVER';

    public function __construct(
        private readonly OtpSmsSender $otpSmsSender,
        private readonly TokenIssuer $tokenIssuer,
    ) {}

    /**
     * Pilot/debug cache key — one entry per OTP challenge, not per phone.
     * A phone+role key would overwrite every historical row on the dev OTP page.
     */
    public static function devPlaintextCacheKey(int $challengeId): string
    {
        return 'otp-dev-plaintext:challenge:'.$challengeId;
    }

    /**
     * @return array{challenge_expires_in_seconds: int, resend_available_in_seconds: int}
     */
    public function requestChallenge(string $phoneRaw, string $roleHint): array
    {
        $normalized = $this->validatedNormalizedPhone($phoneRaw);
        $roleHint = strtoupper(trim($roleHint));

        if (! in_array($roleHint, [self::PASSENGER_ROLE_HINT, self::DRIVER_ROLE_HINT], true)) {
            throw ValidationException::withMessages([
                'role_hint' => ['Invalid role_hint. Use PASSENGER or DRIVER.'],
            ]);
        }

        $hourlyKey = 'otp-hourly:'.$normalized;
        if (RateLimiter::tooManyAttempts($hourlyKey, 5)) {
            throw new OtpHourlyRateLimitedException(RateLimiter::availableIn($hourlyKey));
        }

        $cooldownKey = 'otp-send:'.$normalized.':'.$roleHint;
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            throw new OtpSendCooldownException(RateLimiter::availableIn($cooldownKey));
        }

        $plain = sprintf('%06d', random_int(0, 999999));

        $challenge = DB::transaction(function () use ($normalized, $roleHint, $plain) {
            OtpChallenge::query()
                ->where('phone_number', $normalized)
                ->where('role_hint', $roleHint)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            return OtpChallenge::query()->create([
                'phone_number' => $normalized,
                'role_hint' => $roleHint,
                'otp_hash' => Hash::make($plain),
                'expires_at' => now()->addSeconds(self::OTP_EXPIRY_SECONDS),
                'verify_attempts' => 0,
                'resend_count' => 0,
            ]);
        });

        RateLimiter::hit($hourlyKey, self::HOURLY_DECAY_SECONDS);
        RateLimiter::hit($cooldownKey, self::SEND_COOLDOWN_SECONDS);

        // PRD §5 — pilot has no SMS; cache the plaintext OTP briefly so admins can
        // hand it to testers via the in-panel dev page (only readable when APP_DEBUG is on).
        if (config('app.debug') === true) {
            Cache::put(
                self::devPlaintextCacheKey($challenge->id),
                $plain,
                now()->addSeconds(self::DEV_PLAINTEXT_CACHE_TTL_SECONDS),
            );
        }

        $this->otpSmsSender->send($normalized, $plain);

        return [
            'challenge_expires_in_seconds' => self::OTP_EXPIRY_SECONDS,
            'resend_available_in_seconds' => self::SEND_COOLDOWN_SECONDS,
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, user: array{id: int, role: string, status: string}, scopes: array<int, string>}
     */
    public function verifyChallenge(string $phoneRaw, string $otpCode, ?string $deviceId): array
    {
        $normalized = $this->validatedNormalizedPhone($phoneRaw);

        $roleHint = $this->consumeChallengeAndGetRoleHint($normalized, $otpCode);

        if ($roleHint === self::PASSENGER_ROLE_HINT) {
            return $this->finalizePassengerAuth($normalized, $deviceId);
        }

        return $this->finalizeDriverAuth($normalized, $deviceId);
    }

    public function verifyForPhoneVerification(string $phoneRaw, string $otpCode): string
    {
        $normalized = $this->validatedNormalizedPhone($phoneRaw);

        return $this->consumeChallengeAndGetRoleHint($normalized, $otpCode);
    }

    private function consumeChallengeAndGetRoleHint(string $normalizedPhone, string $otpCode): string
    {
        $otpCode = preg_replace('/\D+/', '', $otpCode) ?? '';
        if (strlen($otpCode) !== 6) {
            throw ValidationException::withMessages([
                'otp_code' => ['OTP code must be 6 digits.'],
            ]);
        }

        $challenges = OtpChallenge::query()
            ->where('phone_number', $normalizedPhone)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->get();

        if ($challenges->isEmpty()) {
            throw ValidationException::withMessages([
                'otp_code' => ['No active OTP challenge for this phone number.'],
            ]);
        }

        /** @var ?OtpChallenge $matched */
        $matched = null;
        /** @var ?OtpChallenge $latestEligibleForAttempts */
        $latestEligibleForAttempts = null;
        $hasAnyUnexpired = false;
        $hasAnyUnlockedUnexpired = false;

        foreach ($challenges as $c) {
            if ($c->expires_at->isPast()) {
                continue;
            }

            $hasAnyUnexpired = true;

            if ($c->locked_at !== null) {
                continue;
            }

            $hasAnyUnlockedUnexpired = true;
            $latestEligibleForAttempts ??= $c;

            if (Hash::check($otpCode, $c->otp_hash)) {
                $matched = $c;
                break;
            }
        }

        if ($matched === null) {
            if (! $hasAnyUnexpired) {
                throw ValidationException::withMessages([
                    'otp_code' => ['OTP code has expired. Request a new code.'],
                ]);
            }

            if (! $hasAnyUnlockedUnexpired) {
                throw new OtpChallengeLockedException('This OTP challenge is locked due to too many failed attempts.');
            }

            $latestEligibleForAttempts->verify_attempts = (int) $latestEligibleForAttempts->verify_attempts + 1;
            if ($latestEligibleForAttempts->verify_attempts >= self::MAX_VERIFY_ATTEMPTS) {
                $latestEligibleForAttempts->locked_at = now();
            }
            $latestEligibleForAttempts->save();

            if ($latestEligibleForAttempts->locked_at !== null) {
                throw new OtpChallengeLockedException('Too many incorrect OTP attempts.');
            }

            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code.'],
            ]);
        }

        return DB::transaction(function () use ($matched, $normalizedPhone) {
            $matched->consumed_at = now();
            $matched->save();

            OtpChallenge::query()
                ->where('phone_number', $normalizedPhone)
                ->where('role_hint', $matched->role_hint)
                ->whereNull('consumed_at')
                ->where('id', '!=', $matched->getKey())
                ->update(['consumed_at' => now()]);

            return $matched->role_hint ?? self::PASSENGER_ROLE_HINT;
        });
    }

    private function validatedNormalizedPhone(string $phoneRaw): string
    {
        $normalized = PhoneNormalizer::normalize($phoneRaw);
        if ($normalized === null || ! PhoneNormalizer::isValid($phoneRaw)) {
            throw ValidationException::withMessages([
                'phone_number' => ['Invalid phone number format.'],
            ]);
        }

        return $normalized;
    }

    /**
     * @return array{access_token: string, refresh_token: string, user: array{id: int, role: string, status: string}, scopes: array<int, string>}
     */
    private function finalizePassengerAuth(string $normalizedPhone, ?string $deviceId): array
    {
        $user = User::query()
            ->where('phone', $normalizedPhone)
            ->where('role', 'passenger')
            ->first();

        if ($user === null) {
            $user = User::query()->create([
                'name' => 'Passenger',
                'email' => 'passenger_'.Str::lower(Str::random(16)).'@tricykab.local',
                'password' => Hash::make(Str::random(32)),
                'role' => 'passenger',
                'phone' => $normalizedPhone,
                'status' => 'ACTIVE',
            ]);
        }

        $this->assertUserActive($user);

        $scopes = [
            'booking:create',
            'booking:read:self',
            'booking:cancel:self',
            'trip:read:self',
            'receipt:read:self',
            'sos:create:self',
            'dispute:create:self',
        ];

        return $this->tokenIssuer->issue($user, 'PASSENGER', $scopes, $deviceId);
    }

    /**
     * @return array{access_token: string, refresh_token: string, user: array{id: int, role: string, status: string}, scopes: array<int, string>}
     */
    private function finalizeDriverAuth(string $normalizedPhone, ?string $deviceId): array
    {
        $user = User::query()
            ->where('phone', $normalizedPhone)
            ->where('role', 'driver')
            ->first();

        if ($user === null) {
            throw new DriverAccountMissingException;
        }

        $this->assertUserActive($user);

        $driver = Driver::query()->where('user_id', $user->id)->first();

        if ($driver === null) {
            throw new DriverAccountMissingException;
        }

        if ($driver->status !== 'active') {
            throw new OperationForbiddenException('Driver account is not approved for operations.');
        }

        $scopes = [
            'availability:update:self',
            'booking:read:self',
            'booking:offer:read:self',
            'booking:accept:self',
            'booking:decline:self',
            'booking:cancel:self',
            'trip:start:self',
            'trip:end:self',
            'trip:update:self',
            'passenger:add:self',
            'payment:record:self',
            'earnings:read:self',
            'compliance:read:self',
        ];

        return $this->tokenIssuer->issue($user, 'DRIVER', $scopes, $deviceId);
    }

    private function assertUserActive(User $user): void
    {
        if (($user->status ?? 'ACTIVE') === 'SUSPENDED') {
            throw new OperationForbiddenException('Account is suspended.');
        }
    }
}
