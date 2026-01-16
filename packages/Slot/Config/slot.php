<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Slot configuration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Slot Duration
    |--------------------------------------------------------------------------
    |
    | The default duration for generated time slots in minutes.
    |
    */
    'default_slot_duration' => 30,

    /*
    |--------------------------------------------------------------------------
    | Buffer Times
    |--------------------------------------------------------------------------
    |
    | Default buffer times before and after appointments in minutes.
    |
    */
    'default_buffer_before' => 0,
    'default_buffer_after' => 0,

    /*
    |--------------------------------------------------------------------------
    | Overlap Rules
    |--------------------------------------------------------------------------
    |
    | Default overlap rules for different schedule types.
    | These can be overridden on individual schedules.
    |
    */
    'overlap_rules' => [
        'availability' => [
            'availability' => true,
            'appointment' => false,
            'blocked' => false,
            'custom' => false,
        ],
        'appointment' => [
            'availability' => false,
            'appointment' => false,
            'blocked' => false,
            'custom' => false,
        ],
        'blocked' => [
            'availability' => true,
            'appointment' => false,
            'blocked' => false,
            'custom' => false,
        ],
        'custom' => [
            'availability' => false,
            'appointment' => false,
            'blocked' => false,
            'custom' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum Slots Per Query
    |--------------------------------------------------------------------------
    |
    | Maximum number of slots to generate per query to prevent performance issues.
    |
    */
    'max_slots_per_query' => 100,

    /*
    |--------------------------------------------------------------------------
    | Notification URLs
    |--------------------------------------------------------------------------
    |
    | Base URLs for different client portal actions.
    |
    */
    'urls' => [
        'view_booking' => 'bookings',
    ],
];
