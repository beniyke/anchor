<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Activity package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity\Providers;

use Activity\Models\Activity;
use Activity\Services\ActivityAnalytics;
use Activity\Services\ActivityManagerService;
use App\Models\User;
use Core\Services\ServiceProvider;
use Helpers\File\Paths;

class ActivityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadHelpers(Paths::packagePath('Activity/Helpers/activity.php'));

        $this->container->singleton(ActivityManagerService::class);
        $this->container->singleton(ActivityAnalytics::class);
    }

    public function boot(): void
    {
        User::macro('activities', function () {
            return $this->hasMany(Activity::class, 'user_id', 'id');
        });

        User::macro('hasActivities', function () {
            return $this->activities()->exists();
        });
    }
}
