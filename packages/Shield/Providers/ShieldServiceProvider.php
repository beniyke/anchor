<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Shield\Providers;

use Core\Services\ServiceProvider;
use Shield\Services\ShieldManagerService;

class ShieldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ShieldManagerService::class);
    }

    public function boot(): void
    {
    }
}
