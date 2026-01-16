<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Scout Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Providers;

use Core\Services\ServiceProvider;
use Scout\Services\RecruitmentManagerService;
use Scout\Services\ScoutAnalyticsService;
use Scout\Services\ScoutManagerService;

class ScoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ScoutManagerService::class);
        $this->container->singleton(RecruitmentManagerService::class);
        $this->container->singleton(ScoutAnalyticsService::class);
    }

    public function boot(): void
    {
        // Registration for events, notifications, etc.
    }
}
