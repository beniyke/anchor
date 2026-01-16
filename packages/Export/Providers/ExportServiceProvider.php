<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Export package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Providers;

use Core\Services\ServiceProvider;
use Export\Services\ExportManagerService;

class ExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ExportManagerService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
