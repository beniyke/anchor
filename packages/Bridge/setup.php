<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Bridge Package Setup Manifest.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Bridge\Providers\BridgeServiceProvider::class,
    ],
    'middleware' => [
        'api' => [
            Bridge\Middleware\BridgeAuthMiddleware::class,
        ],
    ],
];
