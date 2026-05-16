<?php

namespace App\Services\Otp;

use App\Contracts\OtpSmsSender;
use App\Services\Sms\UniSmsService;

/**
 * PRD §5.2 — OTP delivery adapter for UniSMS.
 */
class UniSmsOtpSender implements OtpSmsSender
{
    public function __construct(
        private readonly UniSmsService $uniSmsService,
    ) {}

    public function send(string $phoneE164, string $otpCode): void
    {
        $message = "Hi, Your TricyKab One-Time-Pin is {$otpCode} and valid for 5 mins.";

        $this->uniSmsService->send($phoneE164, $message);
    }
}
