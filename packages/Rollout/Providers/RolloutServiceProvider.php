<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Rollout package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Providers;

use Core\Services\ServiceProvider;
use Rollout\Services\RolloutManagerService;

class RolloutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(RolloutManagerService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
