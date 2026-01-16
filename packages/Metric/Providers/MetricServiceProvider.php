<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Metric Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Providers;

use Core\Services\ServiceProvider;
use Metric\Services\MetricAnalyticsService;
use Metric\Services\MetricManagerService;
use Metric\Services\PerformanceManagerService;

class MetricServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(MetricManagerService::class);
        $this->container->singleton(PerformanceManagerService::class);
        $this->container->singleton(MetricAnalyticsService::class);
    }

    public function boot(): void
    {
        // Registration for performance-related events
    }
}
