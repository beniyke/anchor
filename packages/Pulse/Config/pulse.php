<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * pulse.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /**
     * Reputation points configuration.
     */
    'reputation' => [
        'points_per_thread' => 10,
        'points_per_post' => 2,
        'points_per_reaction' => 1,
    ],

    /**
     * Moderation settings.
     */
    'moderation' => [
        'auto_lock_reports' => 5, // Lock thread after X reports
        'require_verification' => true, // Users must be verified to post
    ],

    /**
     * Discussion settings.
     */
    'threads' => [
        'per_page' => 20,
        'slug_limit' => 100,
    ],
];
