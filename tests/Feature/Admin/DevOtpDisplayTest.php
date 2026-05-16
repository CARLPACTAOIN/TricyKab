<?php

use App\Contracts\OtpSmsSender;
use App\Models\OtpChallenge;
use App\Models\User;
use App\Services\Otp\OtpChallengeService;
use Illuminate\Support\Facades\Cache;
use Tests\Support\CapturingOtpSmsSender;

beforeEach(function () {
    config(['app.debug' => true]);
    $sender = new CapturingOtpSmsSender;
    app()->instance(OtpSmsSender::class, $sender);
    $this->sender = $sender;
});

it('caches dev plaintext per challenge id not per phone', function () {
    $phone = '+639773146853';

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'DRIVER',
    ])->assertOk();

    $first = OtpChallenge::query()->where('phone_number', $phone)->latest('id')->firstOrFail();
    $firstCode = Cache::get(OtpChallengeService::devPlaintextCacheKey($first->id));
    expect($firstCode)->toBe($this->sender->lastCode);

    $this->travel(61)->seconds();

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'DRIVER',
    ])->assertOk();

    $second = OtpChallenge::query()->where('phone_number', $phone)->latest('id')->firstOrFail();
    $secondCode = Cache::get(OtpChallengeService::devPlaintextCacheKey($second->id));

    expect($second->id)->not->toBe($first->id);
    expect($secondCode)->toBe($this->sender->lastCode);
    expect(Cache::get(OtpChallengeService::devPlaintextCacheKey($first->id)))->toBe($firstCode);
    expect($firstCode)->not->toBe($secondCode);
});

it('shows each challenge otp only on its dev page row', function () {
    $phone = '+639773146853';

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'DRIVER',
    ])->assertOk();
    $firstCode = $this->sender->lastCode;

    $this->travel(61)->seconds();

    $this->postJson('/api/v1/auth/otp/request', [
        'phone_number' => $phone,
        'role_hint' => 'DRIVER',
    ])->assertOk();
    $secondCode = $this->sender->lastCode;

    $admin = User::factory()->create([
        'role' => 'admin',
        'toda_id' => null,
    ]);

    $html = $this->actingAs($admin)->get(route('admin.dev.otp'))->assertOk()->getContent();

    expect(substr_count($html, $firstCode))->toBe(1);
    expect(substr_count($html, $secondCode))->toBe(1);
});
