<?php

namespace App\Services\Sms;

/**
 * PRD §5.2 — SMS gateway abstraction.
 *
 * All OTP delivery goes through this contract so the underlying provider
 * (UniSMS, Vonage, InfoBip, etc.) can be swapped via a single config
 * binding without touching business logic.
 */
interface SmsServiceInterface
{
    /**
     * Send an SMS message to the given phone number.
     *
     * @param  string  $to  E.164 or local format phone number (e.g. +639171234567)
     * @param  string  $message  Plain-text message body (max 160 chars for single SMS)
     * @return bool True on delivery acceptance, false on provider error
     */
    public function send(string $to, string $message): bool;
}
