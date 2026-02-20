<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for converting tenant paths.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Providers;

use Core\Services\ServiceProvider;
use Helpers\File\Paths;
use Tenancy\TenantManager;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(TenantManager::class);

        $this->loadHelpers(Paths::packagePath('Tenancy/Helpers/tenant.php'));
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
