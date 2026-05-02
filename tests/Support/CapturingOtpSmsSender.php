<?php

namespace Tests\Support;

use App\Contracts\OtpSmsSender;

final class CapturingOtpSmsSender implements OtpSmsSender
{
    public ?string $lastCode = null;

    public ?string $lastPhone = null;

    public function send(string $phoneE164, string $otpCode): void
    {
        $this->lastPhone = $phoneE164;
        $this->lastCode = $otpCode;
    }
}
