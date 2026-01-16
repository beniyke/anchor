<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * stack.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Stack Default Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define settings for the Stack form builder package.
    |
    */

    'submissions' => [
        'limit_per_ip' => 60, // Per hour
        'store_ip' => true,
        'store_user_agent' => true,
        'retention_days' => 30, // Keep submissions for 30 days
    ],

    'notifications' => [
        'email_on_submission' => true,
    ],
];
