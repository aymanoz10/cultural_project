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

    'whatsapp' => [
        'mode'            => env('WHATSAPP_MODE', 'log'),
        'token'           => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'api_version'     => env('WHATSAPP_API_VERSION', 'v21.0'),
        'otp_template'    => env('WHATSAPP_OTP_TEMPLATE'),
        'otp_language'    => env('WHATSAPP_OTP_LANGUAGE', 'ar'),
        'instance_id'     => env('WHATSAPP_INSTANCE_ID'),
    ],

    'otp' => [
        'expires_minutes' => env('OTP_EXPIRES_MINUTES', 5),
        'max_attempts'    => env('OTP_MAX_ATTEMPTS', 5),
    ],

    'fcm' => [
        'mode'        => env('FCM_MODE', 'log'),
        // FCM HTTP v1 — المصادقة عبر حساب خدمة (Service Account)
        'project_id'  => env('FCM_PROJECT_ID'),
        'credentials' => env('FCM_CREDENTIALS'), // المسار المطلق لملف service-account.json
        // المفتاح القديم (Legacy) — لم يعد مستخدمًا بعد إيقاف Google له
        'server_key'  => env('FCM_SERVER_KEY'),
    ],

];
