<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link Package Setup Manifest.
 * Provides timed token-based access to resources.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Link\Providers\LinkServiceProvider::class,
    ],
    'middleware' => [],
];
