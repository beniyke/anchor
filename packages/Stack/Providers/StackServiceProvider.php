<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ServiceProvider for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Providers;

use Core\Services\ServiceProvider;
use Stack\Services\StackAnalyticsService;
use Stack\Services\StackManagerService;

class StackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(StackManagerService::class, function () {
            return new StackManagerService();
        });

        $this->container->singleton(StackAnalyticsService::class, function () {
            return new StackAnalyticsService();
        });
    }

    public function boot(): void
    {
        // Boot logic here
    }
}
