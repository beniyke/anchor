<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Support package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Providers;

use Core\Services\ServiceProvider;
use Support\Services\SupportManagerService;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(SupportManagerService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
