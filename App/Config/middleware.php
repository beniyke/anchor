<?php

declare(strict_types=1);
/**
 * Middleware classes applied per route group
 */

return [
    'web' => [
        App\Middleware\Web\SessionMiddleware::class,
        Security\Firewall\Middleware\FirewallMiddleware::class,
        App\Middleware\Web\RedirectIfAuthenticatedMiddleware::class,
        App\Middleware\Web\WebAuthMiddleware::class,
        App\Middleware\Web\PasswordUpdateMiddleware::class
    ],
    'api' => [
        Security\Firewall\Middleware\FirewallMiddleware::class,
        App\Middleware\Api\ApiAuthMiddleware::class
    ]
];
