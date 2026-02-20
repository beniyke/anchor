<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\SessionService;
use Core\Services\ServiceProvider;
use Database\BaseModel;
use Security\Auth\Interfaces\SessionManagerInterface;

class AppServiceProvider extends ServiceProvider
{
    private static array $singletons = [
        SessionService::class,
    ];

    public function register(): void
    {
        $this->container->singleton(SessionManagerInterface::class, SessionService::class);

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
