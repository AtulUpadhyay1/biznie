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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // SMS OTP (4.7). The key used to be hard-coded in Msg91OtpService.
    'msg91' => [
        'auth_key'        => env('MSG91_AUTH_KEY'),
        'otp_template_id' => env('MSG91_OTP_TEMPLATE_ID'),
        // Off outside production unless explicitly enabled, so local and
        // test runs never text real numbers; the OTP is logged instead.
        'send_sms'        => env('MSG91_SEND_SMS', env('APP_ENV') === 'production'),
    ],

];
