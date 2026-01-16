<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Scribe package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Providers;

use Core\Services\ServiceProvider;
use Scribe\Services\Builders\PostBuilder;
use Scribe\Services\ScribeAnalyticsService;
use Scribe\Services\ScribeManagerService;

class ScribeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ScribeManagerService::class);
        $this->container->singleton(ScribeAnalyticsService::class);
        $this->container->bind(PostBuilder::class);
    }

    public function boot(): void
    {
        // Boot logic (e.g., registering event listeners)
    }
}
