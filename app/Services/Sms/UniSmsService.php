<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PRD §5.2 — Production SMS gateway via UniSMS (Philippines).
 *
 * Delivers backend-generated plaintext OTP messages. UniSMS is delivery-only;
 * code generation, hashing, and verification remain in OtpChallengeService.
 *
 * API docs: https://unismsapi.com/docs/sms
 */
class UniSmsService implements SmsServiceInterface
{
    public function send(string $to, string $message): bool
    {
        $secretKey = config('services.unisms.api_secret_key');

        if (empty($secretKey)) {
            Log::warning('unisms.sms.no_api_key', [
                'phone' => $to,
                'hint' => 'Set UNISMS_API_SECRET_KEY in .env to enable real SMS delivery.',
            ]);

            return false;
        }

        $baseUrl = rtrim((string) config('services.unisms.base_url', 'https://unismsapi.com/api'), '/');
        $senderId = config('services.unisms.sender_id', 'TricyKab');

        $payload = [
            'recipient' => $to,
            'content' => $message,
        ];

        if (! empty($senderId)) {
            $payload['sender_id'] = $senderId;
        }

        try {
            $response = Http::timeout(10)
                ->withBasicAuth($secretKey, '')
                ->acceptJson()
                ->post($baseUrl.'/sms', $payload);

            if ($response->successful()) {
                Log::info('unisms.sms.sent', [
                    'phone' => $to,
                    'status' => $response->status(),
                ]);

                return true;
            }

            Log::error('unisms.sms.failed', [
                'phone' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('unisms.sms.exception', [
                'phone' => $to,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }
}
