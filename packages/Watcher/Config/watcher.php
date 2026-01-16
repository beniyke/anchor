<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Watcher Monitoring System Configuration.
 * This file controls the behavior of the Watcher monitoring system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Watcher
    |--------------------------------------------------------------------------
    |
    | Set to false to completely disable Watcher monitoring.
    |
     */
    'enabled' => env('WATCHER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Event Types
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific event types.
    |
     */
    'types' => [
        'request' => true,
        'query' => true,
        'exception' => true,
        'job' => true,
        'log' => false, // High volume - disabled by default
        'cache' => false,
        'mail' => false,
        'ghost' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sampling Rates
    |--------------------------------------------------------------------------
    |
    | Control what percentage of events to record (0.0 to 1.0).
    | 1.0 = 100%, 0.1 = 10%, 0.0 = 0%
    |
     */
    'sampling' => [
        'request' => 1.0,
        'query' => 1.0,
        'exception' => 1.0, // Always record exceptions
        'job' => 1.0,
        'log' => 0.1, // Only 10% of logs
        'cache' => 0.1,
        'mail' => 1.0,
        'ghost' => 1.0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Configuration
    |--------------------------------------------------------------------------
    |
    | Batch writes reduce database load.
    |
     */
    'batch' => [
        'enabled' => true,
        'size' => 50, // Flush after 50 entries
        'flush_interval' => 5, // Or after 5 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    |
    | How many days to keep each event type (in days).
    |
     */
    'retention' => [
        'request' => 7,
        'query' => 7,
        'exception' => 30,
        'job' => 14,
        'log' => 7,
        'cache' => 1,
        'mail' => 7,
        'ghost' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    | Ignore specific paths, queries, or redact sensitive fields.
    |
     */
    'filters' => [
        'ignore_paths' => [
            '/health',
            '/metrics',
            '/favicon.ico',
        ],
        'ignore_queries' => [
            'SELECT 1',
        ],
        'redact_fields' => [
            'password',
            'token',
            'secret',
            'api_key',
            'credit_card',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts (Future Feature)
    |--------------------------------------------------------------------------
    |
    | Threshold-based alerting configuration.
    |
     */
    'alerts' => [
        'enabled' => false,
        'thresholds' => [
            'error_rate' => 5.0, // percent
            'slow_query' => 1000, // ms
            'slow_request' => 2000, // ms
        ],
        'channels' => [
            'email' => [],
            'slack' => null,
            'webhook' => null,
        ],
        'throttle' => 300, // seconds between same alert
    ],
];
