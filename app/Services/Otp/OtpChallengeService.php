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
use App\Support\PhoneNormalizer;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
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
        private readonly OtpSmsSender $otpSmsSender
    ) {}

    public static function devPlaintextCacheKey(string $normalizedPhone, string $roleHint): string
    {
        return 'otp-dev-plaintext:'.$normalizedPhone.':'.strtoupper($roleHint);
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

        OtpChallenge::query()
            ->where('phone_number', $normalized)
            ->where('role_hint', $roleHint)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $plain = sprintf('%06d', random_int(0, 999999));

        $challenge = OtpChallenge::query()->create([
            'phone_number' => $normalized,
            'role_hint' => $roleHint,
            'otp_hash' => Hash::make($plain),
            'expires_at' => now()->addSeconds(self::OTP_EXPIRY_SECONDS),
            'verify_attempts' => 0,
            'resend_count' => 0,
        ]);

        RateLimiter::hit($hourlyKey, self::HOURLY_DECAY_SECONDS);
        RateLimiter::hit($cooldownKey, self::SEND_COOLDOWN_SECONDS);

        // PRD §5 — pilot has no SMS; cache the plaintext OTP briefly so admins can
        // hand it to testers via the in-panel dev page (only readable when APP_DEBUG is on).
        if (config('app.debug') === true) {
            Cache::put(
                self::devPlaintextCacheKey($normalized, $roleHint),
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

        $otpCode = preg_replace('/\D+/', '', $otpCode) ?? '';
        if (strlen($otpCode) !== 6) {
            throw ValidationException::withMessages([
                'otp_code' => ['OTP code must be 6 digits.'],
            ]);
        }

        $challenge = OtpChallenge::query()
            ->where('phone_number', $normalized)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if ($challenge === null) {
            throw ValidationException::withMessages([
                'otp_code' => ['No active OTP challenge for this phone number.'],
            ]);
        }

        if ($challenge->locked_at !== null) {
            throw new OtpChallengeLockedException('This OTP challenge is locked due to too many failed attempts.');
        }

        if ($challenge->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp_code' => ['OTP code has expired. Request a new code.'],
            ]);
        }

        if (! Hash::check($otpCode, $challenge->otp_hash)) {
            $challenge->verify_attempts = (int) $challenge->verify_attempts + 1;
            if ($challenge->verify_attempts >= self::MAX_VERIFY_ATTEMPTS) {
                $challenge->locked_at = now();
            }
            $challenge->save();

            if ($challenge->locked_at !== null) {
                throw new OtpChallengeLockedException('Too many incorrect OTP attempts.');
            }

            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code.'],
            ]);
        }

        $challenge->consumed_at = now();
        $challenge->save();

        OtpChallenge::query()
            ->where('phone_number', $normalized)
            ->where('role_hint', $challenge->role_hint)
            ->whereNull('consumed_at')
            ->where('id', '!=', $challenge->getKey())
            ->update(['consumed_at' => now()]);

        $roleHint = $challenge->role_hint ?? self::PASSENGER_ROLE_HINT;

        if ($roleHint === self::PASSENGER_ROLE_HINT) {
            return $this->finalizePassengerAuth($normalized, $deviceId);
        }

        return $this->finalizeDriverAuth($normalized, $deviceId);
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

        return $this->issueTokensAndPayload($user, 'PASSENGER', $scopes, $deviceId);
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

        return $this->issueTokensAndPayload($user, 'DRIVER', $scopes, $deviceId);
    }

    private function assertUserActive(User $user): void
    {
        if (($user->status ?? 'ACTIVE') === 'SUSPENDED') {
            throw new OperationForbiddenException('Account is suspended.');
        }
    }

    /**
     * @param  array<int, string>  $scopes
     * @return array{access_token: string, refresh_token: string, user: array{id: int, role: string, status: string}, scopes: array<int, string>}
     */
    private function issueTokensAndPayload(User $user, string $roleUpper, array $scopes, ?string $deviceId): array
    {
        $suffix = $deviceId !== null && $deviceId !== '' ? ':'.$deviceId : '';

        $accessExpires = CarbonImmutable::now()->addHours(12);
        $refreshExpires = CarbonImmutable::now()->addDays(30);

        $accessToken = $user->createToken('access'.$suffix, $scopes, $accessExpires)->plainTextToken;
        $refreshToken = $user->createToken('refresh'.$suffix, ['refresh'], $refreshExpires)->plainTextToken;

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => [
                'id' => $user->id,
                'role' => $roleUpper,
                'status' => $user->status ?? 'ACTIVE',
            ],
            'scopes' => $scopes,
        ];
    }
}
