<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Captcha Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default captcha driver that will be used by the
    | framework. You may set this to any of the connections defined in the
    | "drivers" array below.
    |
    | Supported: "recaptcha", "turnstile"
    |
    */

    'default' => env('SHIELD_DRIVER', 'recaptcha'),

    /*
    |--------------------------------------------------------------------------
    | Captcha Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for each driver.
    |
    */

    'drivers' => [
        'recaptcha' => [
            'site_key' => env('RECAPTCHA_SITE_KEY', ''),
            'secret' => env('RECAPTCHA_SECRET', ''),
        ],

        'turnstile' => [
            'site_key' => env('TURNSTILE_SITE_KEY', ''),
            'secret' => env('TURNSTILE_SECRET', ''),
        ],
    ],
];
