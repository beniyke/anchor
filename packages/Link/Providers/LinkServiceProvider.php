<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Link package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Providers;

use Core\Services\ServiceProvider;
use Link\Services\LinkAnalyticsService;
use Link\Services\LinkManagerService;

class LinkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(LinkManagerService::class);
        $this->container->singleton(LinkAnalyticsService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
