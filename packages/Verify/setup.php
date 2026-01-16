<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Verify Package Setup Manifest
 *
 * This file defines what gets registered when the Verify package is installed.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Verify\Providers\VerifyServiceProvider::class,
    ],
    'middleware' => [],
];
