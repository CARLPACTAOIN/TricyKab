<?php

use App\Contracts\OtpSmsSender;
use App\Models\User;
use Tests\Support\CapturingOtpSmsSender;

function bindCapturingOtpSender(): CapturingOtpSmsSender
{
    $sender = new CapturingOtpSmsSender;
    app()->instance(OtpSmsSender::class, $sender);

    return $sender;
}

it('returns envelope data from POST /api/v1/auth/otp/request', function () {
    $sender = bindCapturingOtpSender();

    $response = $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => '+639171234567',
        'role_hint' => 'PASSENGER',
        'device_id' => 'device-abc',
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.challenge_expires_in_seconds', 300);
    $response->assertJsonPath('data.resend_available_in_seconds', 60);
    expect($sender->lastCode)->not->toBeNull()->and(strlen($sender->lastCode))->toBe(6);
});

it('verifies passenger otp and returns tokens and scopes', function () {
    $sender = bindCapturingOtpSender();

    $phone = '+639189991112';

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'PASSENGER',
        'device_id' => 'dev-1',
    ])->assertOk();

    $verify = $this->postJson('/api/v1/auth/otp/verify', [
        'phone_number' => $phone,
        'otp_code' => $sender->lastCode,
        'device_id' => 'dev-1',
    ]);

    $verify->assertOk();
    $verify->assertJsonPath('success', true);
    $verify->assertJsonPath('data.user.role', 'PASSENGER');
    $verify->assertJsonPath('data.user.status', 'ACTIVE');
    $verify->assertJsonStructure([
        'data' => [
            'access_token',
            'refresh_token',
            'user' => ['id', 'role', 'status'],
            'scopes',
        ],
    ]);

    expect(User::query()->where('phone', $phone)->where('role', 'passenger')->exists())->toBeTrue();
});

it('returns 429 with retry_after_seconds on cooldown', function () {
    bindCapturingOtpSender();

    $payload = [
        'phone_number' => '+639171444444',
        'role_hint' => 'PASSENGER',
    ];

    $this->postJson('/api/v1/auth/otp/request', $payload)->assertOk();

    $blocked = $this->postJson('/api/v1/auth/otp/request', $payload);
    $blocked->assertStatus(429);
    $blocked->assertJsonPath('success', false);
    $blocked->assertJsonPath('error.code', 'RATE_LIMITED');
    $blocked->assertJsonStructure(['error' => ['details' => ['retry_after_seconds']]]);
});

it('returns 423 after five incorrect otp attempts', function () {
    $sender = bindCapturingOtpSender();

    $phone = '+639171555555';

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'PASSENGER',
    ])->assertOk();

    for ($i = 0; $i < 4; $i++) {
        $this->postJson('/api/v1/auth/otp/verify', [
            'phone_number' => $phone,
            'otp_code' => '000000',
        ])->assertStatus(422);
    }

    $locked = $this->postJson('/api/v1/auth/otp/verify', [
        'phone_number' => $phone,
        'otp_code' => '000000',
    ]);

    $locked->assertStatus(423);
    $locked->assertJsonPath('error.code', 'OTP_LOCKED');
});

it('returns 403 for inactive seeded driver', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    $sender = bindCapturingOtpSender();

    $phone = '+639171230001';

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'DRIVER',
    ])->assertOk();

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'phone_number' => $phone,
        'otp_code' => $sender->lastCode,
    ]);

    $response->assertForbidden();
    $response->assertJsonPath('error.code', 'FORBIDDEN');
});

it('returns success for active seeded driver', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    $sender = bindCapturingOtpSender();

    $phone = '+639171100000';

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'DRIVER',
    ])->assertOk();

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'phone_number' => $phone,
        'otp_code' => $sender->lastCode,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.user.role', 'DRIVER');
    expect($response->json('data.scopes'))->toContain('booking:accept:self');
});

it('returns 429 after five otp requests in one hour when cooldown bypassed', function () {
    bindCapturingOtpSender();

    $payload = [
        'phone_number' => '+639171666666',
        'role_hint' => 'PASSENGER',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/v1/auth/otp/request', $payload)->assertOk();
        $this->travel(61)->seconds();
    }

    $sixth = $this->postJson('/api/v1/auth/otp/request', $payload);
    $sixth->assertStatus(429);
    $sixth->assertJsonPath('error.code', 'RATE_LIMITED');
    expect($sixth->json('error.details.retry_after_seconds'))->toBeInt();
});
