<?php

namespace App\Exceptions\Otp;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class OtpSendCooldownException extends RuntimeException
{
    public function __construct(public int $retryAfterSeconds)
    {
        parent::__construct('Please wait before requesting another OTP.');
    }

    public function render(Request $request): ?Response
    {
        if ($request->is('api/*')) {
            return ApiResponse::error(
                'RATE_LIMITED',
                $this->getMessage(),
                429,
                ['retry_after_seconds' => $this->retryAfterSeconds]
            );
        }

        return null;
    }
}
