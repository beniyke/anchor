<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Closure;
use Core\Middleware\MiddlewareInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;

class PasswordUpdateMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if (! $this->auth->isAuthenticated()) {
            return $next($request, $response);
        }

        $maxAgeInDays = $this->config->get('auth.password_max_age_days');
        $user = $this->auth->user();

        if (! $user) {
            return $next($request, $response);
        }

        $isUpdateRoute = $request->route() === $request->routeName('change-password');
        $shouldForceUpdate = $user->passwordNeedsUpdate($maxAgeInDays);
        $isExemptedRoute = $isUpdateRoute || $request->isLogoutRoute();

        if ($shouldForceUpdate && ! $isExemptedRoute) {
            return $response->redirect($request->fullRouteByName('change-password'));
        }

        return $next($request, $response);
    }
}
