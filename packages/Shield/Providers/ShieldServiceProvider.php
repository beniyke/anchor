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
        // Register global helper if needed, or leave to composer autoloading
        // if file-based. Since we decided on a dynamic helper 'shield()',
        // it might be best defined in a Helpers file loaded here or by composer.
    }
}
