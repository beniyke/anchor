<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Configuration
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | OTP Code Length
    |--------------------------------------------------------------------------
    |
    | Length of the OTP code in digits (4-8 digits supported)
    |
    */
    'code_length' => env('VERIFY_CODE_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | Code Expiration
    |--------------------------------------------------------------------------
    |
    | How long OTP codes remain valid (in minutes)
    |
    */
    'expiration_minutes' => env('VERIFY_EXPIRATION_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting - Generation
    |--------------------------------------------------------------------------
    |
    | Maximum number of OTP codes that can be generated per time window
    |
    */
    'rate_limit_generation' => env('VERIFY_RATE_LIMIT_GENERATION', 3),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting - Time Window
    |--------------------------------------------------------------------------
    |
    | Time window for rate limiting (in minutes)
    |
    */
    'rate_limit_window_minutes' => env('VERIFY_RATE_LIMIT_WINDOW', 60),

    /*
    |--------------------------------------------------------------------------
    | Maximum Verification Attempts
    |--------------------------------------------------------------------------
    |
    | Maximum number of verification attempts allowed before lockout
    |
    */
    'max_verification_attempts' => env('VERIFY_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Delivery Channels
    |--------------------------------------------------------------------------
    |
    | Available channels for OTP delivery. You can add custom channels here.
    |
    */
    'channels' => [
        'email' => Verify\Channels\EmailChannel::class,
        'sms' => Verify\Channels\SmsChannel::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Channel
    |--------------------------------------------------------------------------
    |
    | The default channel to use when none is specified
    |
    */
    'default_channel' => env('VERIFY_DEFAULT_CHANNEL', 'email'),
];
