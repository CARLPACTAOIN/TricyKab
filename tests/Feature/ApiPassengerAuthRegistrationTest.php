<?php

use App\Contracts\OtpSmsSender;
use App\Models\User;
use Tests\Support\CapturingOtpSmsSender;

function bindCapturingOtpSenderForPassengerReg(): CapturingOtpSmsSender
{
    $sender = new CapturingOtpSmsSender;
    app()->instance(OtpSmsSender::class, $sender);

    return $sender;
}

it('registers passenger, verifies phone via otp, then logs in with email/password', function () {
    $sender = bindCapturingOtpSenderForPassengerReg();

    $payload = [
        'email' => 'new_passenger@example.com',
        'password' => 'password123',
        'first_name' => 'Ana',
        'last_name' => 'Santos',
        'phone_number' => '+639171234567',
    ];

    $register = $this->postJson('/api/v1/passenger/register', $payload);
    $register->assertOk();
    $register->assertJsonPath('success', true);
    expect($sender->lastCode)->not->toBeNull();

    $user = User::query()->where('email', $payload['email'])->where('role', 'passenger')->first();
    expect($user)->not->toBeNull()
        ->and($user->phone_verified_at)->toBeNull();

    $verify = $this->postJson('/api/v1/passenger/verify-phone', [
        'email' => $payload['email'],
        'phone_number' => $payload['phone_number'],
        'otp_code' => $sender->lastCode,
    ]);
    $verify->assertOk();
    $verify->assertJsonPath('data.verified', true);

    $user->refresh();
    expect($user->phone_verified_at)->not->toBeNull();

    $login = $this->postJson('/api/v1/passenger/login', [
        'email' => $payload['email'],
        'password' => $payload['password'],
    ]);
    $login->assertOk();
    $login->assertJsonPath('data.user.role', 'PASSENGER');
    $login->assertJsonStructure([
        'data' => [
            'access_token',
            'refresh_token',
            'user' => ['id', 'role', 'status'],
            'scopes',
        ],
    ]);
});

it('blocks login when phone is not verified', function () {
    $sender = bindCapturingOtpSenderForPassengerReg();

    $payload = [
        'email' => 'unverified_passenger@example.com',
        'password' => 'password123',
        'first_name' => 'Ben',
        'last_name' => 'Reyes',
        'phone_number' => '+639171234568',
    ];

    $this->postJson('/api/v1/passenger/register', $payload)->assertOk();
    expect($sender->lastCode)->not->toBeNull();

    $login = $this->postJson('/api/v1/passenger/login', [
        'email' => $payload['email'],
        'password' => $payload['password'],
    ]);

    $login->assertForbidden();
    $login->assertJsonPath('error.code', 'FORBIDDEN');
});

