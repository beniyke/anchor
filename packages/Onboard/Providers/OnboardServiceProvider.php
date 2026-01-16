<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Onboard Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Providers;

use Core\Services\ServiceProvider;
use Onboard\Services\OnboardAnalyticsService;
use Onboard\Services\OnboardManagerService;
use Onboard\Services\TrainingManagerService;

class OnboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(OnboardManagerService::class);
        $this->container->singleton(TrainingManagerService::class);
        $this->container->singleton(OnboardAnalyticsService::class);
    }

    public function boot(): void
    {
        // Registration for events, listeners, etc.
    }
}
