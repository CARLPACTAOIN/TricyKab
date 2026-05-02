<?php

namespace App\Exceptions\Otp;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class DriverAccountMissingException extends RuntimeException
{
    public function __construct(string $message = 'No driver account exists for this phone number.')
    {
        parent::__construct($message);
    }

    public function render(Request $request): ?Response
    {
        if ($request->is('api/*')) {
            return ApiResponse::error('RESOURCE_NOT_FOUND', $this->getMessage(), 422);
        }

        return null;
    }
}
