<?php

declare(strict_types=1);

/**
 * Database configuration settings for the application.
 *
 * This configuration defines the database driver, connections for various database types,
 * and operations related to database management like backups and migrations.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

return [
    /**
     * The database driver to use, defaulting to 'sqlite' if not specified.
     */
    'driver' => env('DB_CONNECTION', 'sqlite'),

    /**
     * Database connections configuration.
     */
    'connections' => [
        /**
         * MySQL database connection settings.
         */
        'mysql' => [
            'host' => env('DB_HOST', 'localhost'),
            'name' => env('DB_DATABASE', 'anchor'),
            'user' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'driver' => 'mysql',
            'persistent' => true,
            'timezone' => env('TIMEZONE', 'UTC'),
            'options' => [],
        ],

        /**
         * SQLite database connection settings.
         */
        'sqlite' => [
            'path' => 'App/storage/database',
            'database' => env('DB_DATABASE', 'anchor.sqlite'),
            'busy_timeout' => null,
            'journal_mode' => 'WAL',
            'synchronous' => null,
            'persistent' => true,
            'options' => [],
        ],
    ],

    /**
     * Operations related to database management.
     */
    'operations' => [
        /**
         * Backup operation settings.
         */
        'backup' => [
            'name' => 'backup',
            'path' => 'App/storage/database/backup',
        ],

        /**
         * Path for database migration files.
         */
        'migrations' => 'App/storage/database/migrations',

        /**
         * Path for database seed files.
         */
        'seeds' => 'App/storage/database/seeds',
    ],

    /**
     * Slow Query threshold in illiseconds
     */
    'logging' => [
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD_MS', 500),
    ],
];
