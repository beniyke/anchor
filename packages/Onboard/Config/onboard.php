<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * onboard.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /**
     * Default onboarding settings.
     */
    'defaults' => [
        'due_days' => 30, // Default due date from start
    ],

    /**
     * Integration settings.
     */
    'integrations' => [
        'sync_with_flow' => true,
        'notify_manager_on_completion' => true,
    ],
];
