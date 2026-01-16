<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Import package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Providers;

use Core\Services\ServiceProvider;
use Import\Services\Importers\CsvImporter;
use Import\Services\ImportManagerService;

class ImportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ImportManagerService::class);
        $this->container->singleton(CsvImporter::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
