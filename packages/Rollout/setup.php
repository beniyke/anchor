<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rollout Package Setup
 *
 * Feature flags and A/B testing for the Anchor Framework.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Rollout\Providers\RolloutServiceProvider::class,
    ],
    'middleware' => [],
];
