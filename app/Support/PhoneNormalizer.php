<?php

namespace App\Support;

final class PhoneNormalizer
{
    /**
     * Normalize Philippine mobile numbers to E.164 (+63XXXXXXXXXX).
     */
    public static function normalize(?string $input): ?string
    {
        if ($input === null || $input === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $input);
        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '+63'.substr($digits, 1);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            return '+63'.$digits;
        }

        if (str_starts_with($digits, '63') && strlen($digits) === 11) {
            return '+'.$digits;
        }

        return null;
    }

    public static function isValid(?string $input): bool
    {
        $n = self::normalize($input);

        return $n !== null && preg_match('/^\+63[0-9]{10}$/', $n) === 1;
    }
}
