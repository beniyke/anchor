<?php

declare(strict_types=1);

namespace App\Auth\Providers;

use App\Auth\Listeners\LoginFailedListener;
use App\Auth\Listeners\LoginListener;
use App\Auth\Listeners\LogoutListener;
use App\Services\Auth\ApiAuthService;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\Auth\WebAuthService;
use Core\Event;
use Core\Services\ServiceProvider;
use Helpers\Http\Request;
use Security\Auth\Events\LoginEvent;
use Security\Auth\Events\LoginFailedEvent;
use Security\Auth\Events\LogoutEvent;

class AuthServiceProvider extends ServiceProvider
{
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

    public function boot(): void
    {
        Event::listen(LoginEvent::class, LoginListener::class);
        Event::listen(LogoutEvent::class, LogoutListener::class);
        Event::listen(LoginFailedEvent::class, LoginFailedListener::class);
    }
}
