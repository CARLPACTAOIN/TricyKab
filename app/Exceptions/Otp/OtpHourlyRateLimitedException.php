<?php

namespace App\Exceptions\Otp;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class OtpHourlyRateLimitedException extends RuntimeException
{
    public function __construct(public int $retryAfterSeconds)
    {
        parent::__construct('Too many OTP requests for this phone number.');
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
