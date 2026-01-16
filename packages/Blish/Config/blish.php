<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * blish.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Blish Default Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define settings for the Blish newsletter package.
    |
    */

    'subscriber' => [
        'verify' => true, // Use double opt-in by default
    ],

    'campaign' => [
        'throttle' => 100, // Emails per minute (dummy)
    ],
];
