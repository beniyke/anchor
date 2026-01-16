<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * setup.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Metric\Providers\MetricServiceProvider::class,
    ],
    'middleware' => [
        'web' => [],
        'api' => [],
    ],
];
