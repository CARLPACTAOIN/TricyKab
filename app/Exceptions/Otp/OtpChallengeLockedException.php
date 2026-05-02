<?php

namespace App\Exceptions\Otp;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class OtpChallengeLockedException extends RuntimeException
{
    public function render(Request $request): ?Response
    {
        if ($request->is('api/*')) {
            return ApiResponse::error('OTP_LOCKED', $this->getMessage(), 423);
        }

        return null;
    }
}
