<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Auth\ApiAuthService;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\Auth\WebAuthService;
use Core\Services\DeferredServiceProvider;
use Helpers\Http\Request;

class AuthServiceProvider extends DeferredServiceProvider
{
    public static function provides(): array
    {
        return [AuthServiceInterface::class];
    }

    public function register(): void
    {
        $this->container->singleton(WebAuthService::class);
        $this->container->singleton(ApiAuthService::class);

        $this->container->singleton(AuthServiceInterface::class, function ($container) {
            $request = $container->get(Request::class);

            return $request->routeIsApi()
            ? $container->get(ApiAuthService::class)
            : $container->get(WebAuthService::class);
        });
    }
}
