<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Bridge configuration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [

    /*
    |--------------------------------------------------------------------------
    | API Token Expiration
    |--------------------------------------------------------------------------
    |
    | The number of minutes that an API token should remain valid.
    | Set to null for non-expiring tokens.
    |
    */

    'expiration' => 60 * 24 * 30, // 30 days

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix to use for generated tokens. This can help identify the
    | type of token or the application it belongs to.
    |
    */

    'prefix' => 'bridge_',

    /*
    |--------------------------------------------------------------------------
    | Pruning Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the automatic pruning of expired tokens.
    |
    */

    'prune' => [
        'enabled' => true,
        'hours' => 24, // Prune expired tokens older than 24 hours
    ],

];
