<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * release
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Temporary Folder
    |--------------------------------------------------------------------------
    |
    | The folder where the update archive will be downloaded and extracted
    | before installation. This path is relative to the project root.
    |
    */
    'tmp_folder' => 'App/storage/tmp',

    /*
    |--------------------------------------------------------------------------
    | Update Endpoint
    |--------------------------------------------------------------------------
    |
    | The base URL where your updates are hosted.
    | The package will look for:
    | - {endpoint}/release.json
    | - {endpoint}/{archive_name}.zip
    |
    */
    'endpoint' => env('RELEASE_ENDPOINT', 'http://localhost:8888/update'),

    /*
    |--------------------------------------------------------------------------
    | Authorized Users
    |--------------------------------------------------------------------------
    |
    | IDs of users authorized to perform updates.
    | Usage depends on your application's authentication implementation.
    |
    */
    'authorized_users' => [
        1, // Admin ID
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    |
    | Whether to enable maintenance mode during updates.
    | This toggles FIREWALL_ACTIVATE_MAINTENANCE_GUARD in .env.
    |
    */
    'maintenance_mode' => true,

    /*
    |--------------------------------------------------------------------------
    | Backup
    |--------------------------------------------------------------------------
    |
    | Whether to backup overwritten files before updating.
    | Backups are stored in App/storage/backups.
    |
    */
    'backup' => true,

    /*
    |--------------------------------------------------------------------------
    | Exclude from Update
    |--------------------------------------------------------------------------
    |
    | Files or directories to exclude from being overwritten during update.
    | Relative to project root.
    |
    */
    'exclude' => [
        '.env',
        'App/Config/database.php',
        'App/storage',
        'public/uploads',
    ],
];
