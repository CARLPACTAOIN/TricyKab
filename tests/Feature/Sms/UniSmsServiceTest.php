<?php

use App\Services\Sms\UniSmsService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.unisms.api_secret_key' => 'sk_test_secret',
        'services.unisms.sender_id' => 'TricyKab',
        'services.unisms.base_url' => 'https://unismsapi.com/api',
    ]);
});

it('sends sms via unisms with basic auth and e164 recipient', function () {
    Http::fake([
        'unismsapi.com/api/sms' => Http::response([
            'message' => [
                'status' => 'sent',
                'reference_id' => 'msg_test_123',
                'recipient' => '+639171234567',
            ],
        ], 201),
    ]);

    $service = new UniSmsService;
    $result = $service->send('+639171234567', 'Hi, Your TricyKab One-Time-Pin is 123456 and valid for 5 mins.');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://unismsapi.com/api/sms'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('sk_test_secret:'))
            && $request['recipient'] === '+639171234567'
            && $request['content'] === 'Hi, Your TricyKab One-Time-Pin is 123456 and valid for 5 mins.'
            && $request['sender_id'] === 'TricyKab';
    });
});

it('returns false when unisms responds with unauthorized', function () {
    Http::fake([
        'unismsapi.com/api/sms' => Http::response(['error' => 'Unauthorized'], 401),
    ]);

    $service = new UniSmsService;
    $result = $service->send('+639171234567', 'Test message');

    expect($result)->toBeFalse();
});

it('returns false when api secret key is not configured', function () {
    config(['services.unisms.api_secret_key' => null]);

    Http::fake();

    $service = new UniSmsService;
    $result = $service->send('+639171234567', 'Test message');

    expect($result)->toBeFalse();
    Http::assertNothingSent();
});
