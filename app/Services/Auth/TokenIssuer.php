<?php

namespace App\Services\Auth;

use App\Models\User;
use Carbon\CarbonImmutable;

final class TokenIssuer
{
    /**
     * @param  array<int, string>  $scopes
     * @return array{access_token: string, refresh_token: string, user: array{id: int, role: string, status: string}, scopes: array<int, string>}
     */
    public function issue(User $user, string $roleUpper, array $scopes, ?string $deviceId): array
    {
        $suffix = $deviceId !== null && $deviceId !== '' ? ':'.$deviceId : '';

        $accessExpires = CarbonImmutable::now()->addHours(12);
        $refreshExpires = CarbonImmutable::now()->addDays(30);

        $accessToken = $user->createToken('access'.$suffix, $scopes, $accessExpires)->plainTextToken;
        $refreshToken = $user->createToken('refresh'.$suffix, ['refresh'], $refreshExpires)->plainTextToken;

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => [
                'id' => $user->id,
                'role' => $roleUpper,
                'status' => $user->status ?? 'ACTIVE',
            ],
            'scopes' => $scopes,
        ];
    }
}

