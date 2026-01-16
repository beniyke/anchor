<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Export Package Setup
 *
 * Data export functionality (CSV, Excel, PDF) for the Anchor Framework.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Export\Providers\ExportServiceProvider::class,
    ],
    'middleware' => [],
];
