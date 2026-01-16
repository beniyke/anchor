<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Hub configuration file.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Message Settings
    |--------------------------------------------------------------------------
    */
    'max_message_length' => 10000,
    'max_attachments_per_message' => 10,

    /*
    |--------------------------------------------------------------------------
    | Thread Settings
    |--------------------------------------------------------------------------
    */
    'max_members_per_thread' => 100,
    'default_notifications_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Mentions
    |--------------------------------------------------------------------------
    |
    | Pattern used to extract @mentions from message body.
    |
    */
    'mention_pattern' => '/@(\w+)/',

    /*
    |--------------------------------------------------------------------------
    | Reminders
    |--------------------------------------------------------------------------
    */
    'reminder_advance_minutes' => 15,
    'max_snooze_count' => 5,

    /*
    |--------------------------------------------------------------------------
    | Cleanup
    |--------------------------------------------------------------------------
    */
    'retention_days' => 365,

    /*
    |--------------------------------------------------------------------------
    | Notification URLs
    |--------------------------------------------------------------------------
    |
    | URL patterns for notification links. Use {refid} as placeholder.
    |
    */
    'urls' => [
        'thread' => '/hub/thread/{refid}',
        'reminder' => '/hub/reminder/{refid}',
    ],
];
