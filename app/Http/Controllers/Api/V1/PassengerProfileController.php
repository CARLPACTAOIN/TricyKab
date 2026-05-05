<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PassengerProfileUpsertRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class PassengerProfileController extends Controller
{
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return ApiResponse::success([
            'profile' => [
                'id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'home_address' => $user->home_address,
                'emergency_contact_name' => $user->emergency_contact_name,
                'emergency_contact_phone' => $user->emergency_contact_phone,
                'profile_photo_url' => $user->profile_photo_url,
            ],
        ]);
    }

    public function upsert(PassengerProfileUpsertRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user->fill($request->validated());
        $user->save();

        return $this->show($request);
    }
}

