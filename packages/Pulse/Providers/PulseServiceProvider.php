<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Pulse Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Providers;

use Core\Services\ServiceProvider;
use Pulse\Services\EngagementManagerService;
use Pulse\Services\ModerationManagerService;
use Pulse\Services\PulseManagerService;

class PulseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(PulseManagerService::class);
        $this->container->singleton(ModerationManagerService::class);
        $this->container->singleton(EngagementManagerService::class);
    }

    public function boot(): void
    {
        // Registration for events, notifications, etc.
    }
}
