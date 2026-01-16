<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Import Configuration
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Disk
    |--------------------------------------------------------------------------
    |
    | The default disk for storing import files.
    |
    */
    'disk' => env('IMPORT_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Import Path
    |--------------------------------------------------------------------------
    |
    | The path within the disk where imports are stored.
    |
    */
    'path' => 'imports',

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Number of rows to process at a time for memory efficiency.
    |
    */
    'chunk_size' => 500,

    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in bytes (default 10MB).
    |
    */
    'max_file_size' => env('IMPORT_MAX_FILE_SIZE', 10485760),

    /*
    |--------------------------------------------------------------------------
    | Allowed Extensions
    |--------------------------------------------------------------------------
    |
    | Allowed file extensions for import.
    |
    */
    'allowed_extensions' => ['csv', 'xlsx', 'xls', 'json'],

    /*
    |--------------------------------------------------------------------------
    | Skip Duplicates
    |--------------------------------------------------------------------------
    |
    | Whether to skip duplicate rows by default.
    |
    */
    'skip_duplicates' => true,

    /*
    |--------------------------------------------------------------------------
    | Stop on Error
    |--------------------------------------------------------------------------
    |
    | Whether to stop processing on first error.
    |
    */
    'stop_on_error' => false,

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Queue configuration for large imports.
    |
    */
    'queue' => [
        'enabled' => env('IMPORT_QUEUE_ENABLED', true),
        'connection' => env('IMPORT_QUEUE_CONNECTION', 'default'),
        'queue' => env('IMPORT_QUEUE_NAME', 'imports'),
    ],
];
