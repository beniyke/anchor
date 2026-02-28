<?php

declare(strict_types=1);
/**
 * Middleware classes applied per route group
 */

return [
    'web' => [
        App\Middleware\Web\RedirectIfAuthenticatedMiddleware::class,
        App\Middleware\Web\WebAuthMiddleware::class,
        App\Middleware\Web\PasswordUpdateMiddleware::class,
    ],
    'api' => [
        App\Middleware\Api\ApiAuthMiddleware::class
    ]
];
