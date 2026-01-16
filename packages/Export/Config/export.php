<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Export Configuration
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Disk
    |--------------------------------------------------------------------------
    |
    | The default disk for storing export files.
    |
    */
    'disk' => env('EXPORT_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Export Path
    |--------------------------------------------------------------------------
    |
    | The path within the disk where exports are stored.
    |
    */
    'path' => 'exports',

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Number of rows to process at a time for memory efficiency.
    |
    */
    'chunk_size' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Temp Path
    |--------------------------------------------------------------------------
    |
    | Temporary path for building exports.
    |
    */
    'temp_path' => '',

    /*
    |--------------------------------------------------------------------------
    | Retention Days
    |--------------------------------------------------------------------------
    |
    | Number of days to retain export files. Set to 0 to keep forever.
    |
    */
    'retention_days' => env('EXPORT_RETENTION_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Formats
    |--------------------------------------------------------------------------
    |
    | Supported export formats.
    |
    */
    'formats' => [
        'csv' => [
            'extension' => 'csv',
            'mime_type' => 'text/csv',
            'delimiter' => ',',
            'enclosure' => '"',
        ],
        'xlsx' => [
            'extension' => 'xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'pdf' => [
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Queue configuration for large exports.
    |
    */
    'queue' => [
        'enabled' => env('EXPORT_QUEUE_ENABLED', true),
        'connection' => env('EXPORT_QUEUE_CONNECTION', 'default'),
        'queue' => env('EXPORT_QUEUE_NAME', 'exports'),
    ],
];
