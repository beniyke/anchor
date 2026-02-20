<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Activity package configuration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Level
    |--------------------------------------------------------------------------
    |
    | The default importance level assigned to an activity log if none is
    | specified. Supported: info, notice, warning, error, critical.
    |
    */
    'default_level' => 'info',

    /*
    |--------------------------------------------------------------------------
    | Default Tag
    |--------------------------------------------------------------------------
    |
    | The default tag assigned to an activity log if none is specified.
    |
    */
    'default_tag' => 'general',

    /*
    |--------------------------------------------------------------------------
    | Retention Policy (Days)
    |--------------------------------------------------------------------------
    |
    | The number of days to keep activity logs before they are pruned by the
    | activity:prune command.
    |
    */
    'retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Analytics Cache TTL (Seconds)
    |--------------------------------------------------------------------------
    |
    | How long to cache analytics results to prevent heavy database queries
    | on every dashboard load.
    |
    */
    'cache' => [
        'trends' => 1800,       // 30 minutes
        'users' => 3600,        // 1 hour
        'funnels' => 3600,      // 1 hour
        'behavior' => 900,      // 15 minutes
        'channel_stats' => 3600 // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Capture
    |--------------------------------------------------------------------------
    |
    | Whether to automatically capture technical context details.
    |
    */
    'capture' => [
        'ip' => true,
        'user_agent' => true,
        'session' => true,
        'channel' => true,
    ],
];
