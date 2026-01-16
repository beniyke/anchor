<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Audit Configuration
 *
 * Comprehensive activity logging configuration for the Anchor Framework.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable audit logging globally.
    |
    */
    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Tracked Events
    |--------------------------------------------------------------------------
    |
    | Events to track on models using HasAuditTrail trait.
    |
    */
    'events' => [
        'created' => true,
        'updated' => true,
        'deleted' => true,
        'restored' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Attributes
    |--------------------------------------------------------------------------
    |
    | Attributes to exclude from audit diffs globally.
    | Add sensitive fields here.
    |
    */
    'excluded_attributes' => [
        'password',
        'remember_token',
        'api_token',
        'two_factor_secret',
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention Period
    |--------------------------------------------------------------------------
    |
    | Number of days to retain audit logs. Set to 0 to keep forever.
    |
    */
    'retention_days' => env('AUDIT_RETENTION_DAYS', 90),

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
    | Table Name
    |--------------------------------------------------------------------------
    |
    | Database table name for audit logs.
    |
    */
    'table' => 'audit_logs',

    /*
    |--------------------------------------------------------------------------
    | Checksum
    |--------------------------------------------------------------------------
    |
    | Enable tamper detection with checksums on audit logs.
    |
    */
    'checksum' => [
        'enabled' => true,
        'algorithm' => 'sha256',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Queue audit log creation for better performance.
    |
    */
    'queue' => [
        'enabled' => env('AUDIT_QUEUE_ENABLED', false),
        'connection' => env('AUDIT_QUEUE_CONNECTION', 'default'),
        'queue' => env('AUDIT_QUEUE_NAME', 'audit'),
    ],
];
