<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Blish Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Providers;

use Blish\Services\BlishAnalyticsService;
use Blish\Services\BlishManagerService;
use Blish\Services\CampaignProcessorService;
use Core\Services\ServiceProvider;

class BlishServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(BlishManagerService::class, function () {
            return new BlishManagerService();
        });

        $this->container->singleton(BlishAnalyticsService::class, function () {
            return new BlishAnalyticsService();
        });

        $this->container->singleton(CampaignProcessorService::class, function () {
            return new CampaignProcessorService(
                $this->container->get(BlishManagerService::class),
                $this->container->get(BlishAnalyticsService::class)
            );
        });
    }

    public function boot(): void
    {
    }
}
