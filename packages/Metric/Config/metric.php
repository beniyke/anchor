<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * metric.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /**
     * Performance Review settings.
     */
    'reviews' => [
        'default_rating_scale' => 5.0,
        'allow_anonymous_feedback' => true,
    ],

    /**
     * Recognition & Kudos.
     */
    'recognition' => [
        'award_types' => [
            'kudos' => 'Kudos',
            'excellence' => 'Excellence',
            'innovation' => 'Innovation',
            'teamwork' => 'Teamwork',
        ],
    ],

    /**
     * Analytics settings.
     */
    'analytics' => [
        'cache_results' => true,
        'top_performer_limit' => 5,
    ],
];
