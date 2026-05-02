<?php

namespace App\Contracts;

interface OtpSmsSender
{
    public function send(string $phoneE164, string $otpCode): void;
}
