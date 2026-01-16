<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link configuration file.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Expiry
    |--------------------------------------------------------------------------
    |
    | Default expiration time in hours for new links.
    |
    */
    'default_expiry_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | Maximum Expiry
    |--------------------------------------------------------------------------
    |
    | Maximum allowed expiration time in days.
    |
    */
    'max_expiry_days' => 365,

    /*
    |--------------------------------------------------------------------------
    | Token Length
    |--------------------------------------------------------------------------
    |
    | Length of the generated token (before hashing).
    |
    */
    'token_length' => 64,

    /*
    |--------------------------------------------------------------------------
    | Signing Key
    |--------------------------------------------------------------------------
    |
    | Key used for signing URLs. Falls back to app key if not set.
    |
    */
    'signing_key' => null,

    /*
    |--------------------------------------------------------------------------
    | Cleanup
    |--------------------------------------------------------------------------
    |
    | Days to retain expired links before cleanup.
    |
    */
    'retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum links per user per hour.
    |
    */
    'rate_limit' => [
        'enabled' => true,
        'limit' => 100,
    ],
];
