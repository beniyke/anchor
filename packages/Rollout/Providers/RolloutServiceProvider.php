<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Rollout package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Providers;

use App\Models\User;
use Core\Services\ServiceProvider;
use Rollout\Rollout;
use Rollout\Services\RolloutManagerService;

class RolloutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(RolloutManagerService::class);
    }

    public function boot(): void
    {
        $this->registerUserMacros();
    }

    protected function registerUserMacros(): void
    {
        User::macro('hasFeature', function (string $feature): bool {
            return Rollout::isEnabled($feature, $this);
        });

        User::macro('lacksFeature', function (string $feature): bool {
            return !$this->hasFeature($feature);
        });

        User::macro('hasAnyFeature', function (array $features): bool {
            foreach ($features as $feature) {
                if ($this->hasFeature($feature)) {
                    return true;
                }
            }

            return false;
        });

        User::macro('hasAllFeatures', function (array $features): bool {
            foreach ($features as $feature) {
                if (!$this->hasFeature($feature)) {
                    return false;
                }
            }

            return true;
        });
    }
}
