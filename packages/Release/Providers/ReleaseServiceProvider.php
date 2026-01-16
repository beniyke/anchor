<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Service Provider
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Providers;

use Core\Services\ServiceProvider;
use Release\Services\ReleaseManagerService;

class ReleaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ReleaseManagerService::class);
    }

    public function boot(): void
    {
        // Boot logic
    }
}
