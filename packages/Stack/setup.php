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
        Stack\Providers\StackServiceProvider::class,
    ],
    'middleware' => [
        'web' => [],
        'api' => [],
    ],
];
