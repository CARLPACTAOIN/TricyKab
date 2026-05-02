<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OtpRequestRequest;
use App\Http\Requests\Api\V1\OtpVerifyRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Otp\OtpChallengeService;

class OtpAuthController extends Controller
{
    public function requestOtp(OtpRequestRequest $request, OtpChallengeService $service)
    {
        $data = $service->requestChallenge(
            (string) $request->input('phone_number'),
            (string) $request->input('role_hint'),
        );

        return ApiResponse::success($data);
    }

    public function verify(OtpVerifyRequest $request, OtpChallengeService $service)
    {
        $data = $service->verifyChallenge(
            (string) $request->input('phone_number'),
            (string) $request->input('otp_code'),
            $request->input('device_id') !== null ? (string) $request->input('device_id') : null,
        );

        return ApiResponse::success($data);
    }
}
