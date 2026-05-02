<?php

namespace App\Services\Otp;

use App\Contracts\OtpSmsSender;
use Illuminate\Support\Facades\Log;

class LogOtpSmsSender implements OtpSmsSender
{
    public function send(string $phoneE164, string $otpCode): void
    {
        Log::channel('single')->info('OTP SMS (dev)', [
            'phone' => $phoneE164,
            'otp_code' => $otpCode,
        ]);
    }
}
