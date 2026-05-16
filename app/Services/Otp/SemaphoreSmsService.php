<?php

namespace App\Services\Otp;

use App\Contracts\OtpSmsSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PRD §5.2 — Production SMS gateway via Semaphore PH.
 *
 * Sends OTP codes through the Semaphore REST API.
 * Set SEMAPHORE_API_KEY and optionally SEMAPHORE_SENDER_NAME in .env.
 *
 * On any network or API failure, the error is logged and execution continues
 * so a gateway outage never crashes the auth flow (failure is silent to client).
 *
 * Semaphore API docs: https://semaphore.co/docs
 */
class SemaphoreSmsService implements OtpSmsSender
{
    private const API_URL = 'https://api.semaphore.co/api/v4/messages';

    public function send(string $phoneE164, string $otpCode): void
    {
        $apiKey = config('services.semaphore.api_key');
        $senderName = config('services.semaphore.sender_name', 'TricyKab');

        if (empty($apiKey)) {
            Log::warning('semaphore.sms.no_api_key', [
                'phone' => $phoneE164,
                'hint' => 'Set SEMAPHORE_API_KEY in .env to enable real SMS delivery.',
            ]);

            return;
        }

        // Semaphore expects local PH format (e.g. 09171234567), not E.164 (+639171234567).
        $localPhone = preg_replace('/^\+63/', '0', $phoneE164) ?? $phoneE164;

        try {
            $response = Http::timeout(10)->post(self::API_URL, [
                'apikey' => $apiKey,
                'number' => $localPhone,
                'message' => "Your TricyKab verification code is: {$otpCode}. Valid for 5 minutes. Do not share this code.",
                'sendername' => $senderName,
            ]);

            if ($response->successful()) {
                Log::info('semaphore.sms.sent', [
                    'phone' => $localPhone,
                    'status' => $response->status(),
                ]);
            } else {
                Log::error('semaphore.sms.failed', [
                    'phone' => $localPhone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('semaphore.sms.exception', [
                'phone' => $localPhone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
