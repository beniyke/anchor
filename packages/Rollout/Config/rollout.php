<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rollout Configuration
 *
 * Feature flags and A/B testing configuration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable feature flag checking globally.
    |
    */
    'enabled' => env('ROLLOUT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Cache feature flag states for performance.
    |
    */
    'cache' => [
        'enabled' => env('ROLLOUT_CACHE_ENABLED', true),
        'ttl' => env('ROLLOUT_CACHE_TTL', 300), // 5 minutes
        'prefix' => 'rollout:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    |
    | Default state for undefined features.
    |
    */
    'default_state' => false,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the User model.
    |
    */
    'user_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    |
    | Database table names for the Rollout package.
    |
    */
    'tables' => [
        'features' => 'rollout_features',
        'segments' => 'rollout_segments',
        'segment_users' => 'rollout_segment_users',
    ],
];
