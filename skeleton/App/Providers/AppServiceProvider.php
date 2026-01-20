<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\UserService;
use App\Session\Services\SessionService;
use Core\Services\ServiceProvider;
use Database\BaseModel;

class AppServiceProvider extends ServiceProvider
{
    private static array $singletons = [
        SessionService::class,
        UserService::class,
    ];

    public function register(): void
    {
        if (static::$singletons) {
            foreach (static::$singletons as $singleton) {
                $this->container->singleton($singleton);
            }
        }
    }

    public function boot(): void
    {
        BaseModel::automaticallyEagerLoadRelationships();
    }
}
