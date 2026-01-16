<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tenancy Package Setup Manifest.
 * This file defines what gets registered when the Tenancy package is installed.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Tenancy\Providers\TenancyServiceProvider::class,
    ],
    'middleware' => [
        'web' => [
            Tenancy\Middleware\TenantIdentificationMiddleware::class,
        ],
        'api' => [
            Tenancy\Middleware\TenantIdentificationMiddleware::class,
        ],
    ],
];
