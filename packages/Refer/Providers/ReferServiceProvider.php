<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Refer package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Providers;

use Core\Services\ServiceProvider;
use Refer\Services\ReferManagerService;

class ReferServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ReferManagerService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
