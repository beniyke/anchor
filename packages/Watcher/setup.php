<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Watcher configuration and service registration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Watcher\Providers\WatcherServiceProvider::class,
    ],
    'middleware' => [
        'web' => [
            Watcher\Middleware\WatcherMiddleware::class,
        ],
    ],
];
