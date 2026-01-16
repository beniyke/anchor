<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * guide.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Guide Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains settings for the Guide (FAQ/Help Center) package.
    |
    */

    'search' => [
        'limit' => 10,
        'log_enabled' => true,
    ],

    'feedback' => [
        'enabled' => true,
        'require_comment' => false,
    ],

    'analytics' => [
        'track_views' => true,
        'audit_logging' => true,
    ],
];
