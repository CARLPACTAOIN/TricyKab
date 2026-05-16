<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'firebase' => [
        'projection_enabled' => filter_var(
            env('FIREBASE_PROJECTION_ENABLED', false),
            FILTER_VALIDATE_BOOL
        ),
        'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH', storage_path('firebase-service-account.json')),
        'project_id' => env('FIREBASE_PROJECT_ID'),
    ],

    // PRD §5.2 — UniSMS gateway for OTP delivery (delivery-only; codes generated in-app).
    // Set UNISMS_API_SECRET_KEY in .env for production use.
    // Leave blank to fall back to LogOtpSmsSender (dev/debug mode).
    'unisms' => [
        'api_secret_key' => env('UNISMS_API_SECRET_KEY'),
        'sender_id' => env('UNISMS_SENDER_ID', 'TricyKab'),
        'base_url' => env('UNISMS_BASE_URL', 'https://unismsapi.com/api'),
    ],

];
