<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PassengerLoginRequest;
use App\Http\Requests\Api\V1\PassengerRegisterRequest;
use App\Http\Requests\Api\V1\PassengerVerifyPhoneRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Auth\TokenIssuer;
use App\Services\Otp\OtpChallengeService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PassengerAuthController extends Controller
{
    public function register(PassengerRegisterRequest $request, OtpChallengeService $otp): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        $normalizedPhone = PhoneNormalizer::normalize((string) $validated['phone_number']);
        if ($normalizedPhone === null || ! PhoneNormalizer::isValid((string) $validated['phone_number'])) {
            throw ValidationException::withMessages([
                'phone_number' => ['Invalid phone number format.'],
            ]);
        }

        $user = User::query()->create([
            'role' => 'passenger',
            'status' => 'ACTIVE',
            'email' => (string) $validated['email'],
            'password' => Hash::make((string) $validated['password']),
            'first_name' => (string) $validated['first_name'],
            'last_name' => (string) $validated['last_name'],
            'name' => trim(((string) $validated['first_name']).' '.((string) $validated['last_name'])),
            'phone' => $normalizedPhone,
            'phone_verified_at' => null,
        ]);

        $challenge = $otp->requestChallenge($normalizedPhone, 'PASSENGER');

        return ApiResponse::success([
            'user_id' => $user->id,
            ...$challenge,
        ]);
    }

    public function verifyPhone(PassengerVerifyPhoneRequest $request, OtpChallengeService $otp): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        /** @var ?User $user */
        $user = User::query()
            ->where('email', (string) $validated['email'])
            ->where('role', 'passenger')
            ->first();

        if ($user === null) {
            return ApiResponse::error('RESOURCE_NOT_FOUND', 'Passenger account not found.', 422);
        }

        $normalizedPhone = PhoneNormalizer::normalize((string) $validated['phone_number']);
        if ($normalizedPhone === null || ! PhoneNormalizer::isValid((string) $validated['phone_number'])) {
            throw ValidationException::withMessages([
                'phone_number' => ['Invalid phone number format.'],
            ]);
        }

        if ($user->phone !== $normalizedPhone) {
            throw ValidationException::withMessages([
                'phone_number' => ['Phone number does not match this account.'],
            ]);
        }

        $roleHint = $otp->verifyForPhoneVerification($normalizedPhone, (string) $validated['otp_code']);
        if ($roleHint !== 'PASSENGER') {
            return ApiResponse::error('FORBIDDEN', 'OTP role mismatch.', 403);
        }

        if ($user->phone_verified_at === null) {
            $user->phone_verified_at = now();
            $user->save();
        }

        return ApiResponse::success([
            'verified' => true,
            'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
        ]);
    }

    public function login(PassengerLoginRequest $request, TokenIssuer $issuer): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        /** @var ?User $user */
        $user = User::query()
            ->where('email', (string) $validated['email'])
            ->where('role', 'passenger')
            ->first();

        if ($user === null || ! Hash::check((string) $validated['password'], (string) $user->password)) {
            return ApiResponse::error('FORBIDDEN', 'Invalid email or password.', 403);
        }

        if ($user->phone_verified_at === null) {
            return ApiResponse::error('FORBIDDEN', 'Phone number is not verified.', 403);
        }

        $scopes = [
            'booking:create',
            'booking:read:self',
            'booking:cancel:self',
            'trip:read:self',
            'receipt:read:self',
            'sos:create:self',
            'dispute:create:self',
        ];

        $deviceId = $request->input('device_id') !== null ? (string) $request->input('device_id') : null;

        return ApiResponse::success($issuer->issue($user, 'PASSENGER', $scopes, $deviceId));
    }
}

