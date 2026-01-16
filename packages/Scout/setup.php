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
        Scout\Providers\ScoutServiceProvider::class,
    ],
    'middleware' => [
        'web' => [],
        'api' => [],
    ],
];
