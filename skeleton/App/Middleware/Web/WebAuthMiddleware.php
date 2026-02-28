<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use Closure;
use Core\Contracts\AuthServiceInterface;
use Core\Middleware\MiddlewareInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;

class WebAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if ($request->routeShouldBypassAuth()) {
            return $next($request, $response);
        }

        $guards = $request->getRouteContext('guards') ?? ['web'];
        $authenticatedUser = null;
        $activeGuard = null;

        foreach ($guards as $guard) {
            $this->auth->viaGuard($guard);

            if ($this->auth->isAuthenticated() && $this->auth->isAuthorized($request->route())) {
                $authenticatedUser = $this->auth->user();
                $activeGuard = $guard;
                break;
            }
        }

        if (! $authenticatedUser) {
            $this->auth->logout();

            $loginRoute = $request->getRouteContext('login_route');

            if (! $loginRoute && ! empty($guards)) {
                $fallbackGuard = $activeGuard ?? $guards[0] ?? 'web';
                $loginRoute = $this->config->get("auth.guards.{$fallbackGuard}.route.login");
            }

            $loginRoute = $loginRoute ?? 'login';

            return $response->redirect($request->fullRouteByName($loginRoute));
        }

        $request->setAuthenticatedUser($authenticatedUser);
        $request->setRouteContext('auth_guard', $activeGuard);

        if ($authenticatedUser->getAuthId()) {
            $request->setHeader('X-Account-ID', (string) $authenticatedUser->getAuthId());
        }

        return $next($request, $response);
    }
}
