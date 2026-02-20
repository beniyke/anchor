<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Money Package Setup Manifest.
 * Defines the components registered when the Money package is installed.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Money\Providers\MoneyServiceProvider::class,
    ],
    'middleware' => []
];
