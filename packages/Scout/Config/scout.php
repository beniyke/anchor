<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * scout.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /**
     * Default stages for the hiring pipeline.
     */
    'stages' => [
        ['name' => 'Applied', 'slug' => 'applied', 'is_default' => true],
        ['name' => 'Phone Screen', 'slug' => 'phone-screen'],
        ['name' => 'Technical Interview', 'slug' => 'technical-interview'],
        ['name' => 'Offer', 'slug' => 'offer'],
        ['name' => 'Hired', 'slug' => 'hired'],
    ],

    /**
     * Job settings.
     */
    'jobs' => [
        'per_page' => 15,
        'slug_limit' => 100,
    ],

    /**
     * Internal notifications.
     */
    'notifications' => [
        'on_application' => true,
        'on_interview_scheduled' => true,
    ],
];
