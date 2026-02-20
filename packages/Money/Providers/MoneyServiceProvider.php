<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Money package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Providers;

use Core\Services\ServiceProvider;
use Helpers\File\Paths;

class MoneyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadHelpers(Paths::packagePath('Money/Helpers/money.php'));
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
