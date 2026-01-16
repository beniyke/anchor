<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ClientServiceProvider registers the package services into the container.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Providers;

use Client\Services\AnalyticsManagerService;
use Client\Services\ClientManagerService;
use Core\Services\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        $this->container->singleton(ClientManagerService::class);
        $this->container->singleton(AnalyticsManagerService::class);
    }

    public function boot(): void
    {
        // Boot logic here
    }
}
