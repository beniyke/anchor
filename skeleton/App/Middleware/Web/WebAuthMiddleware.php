<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Closure;
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
                $loginRoute = $this->config->get("auth.guards.{$guards[0]}.login_route");
            }

            $loginRoute = $loginRoute ?? 'login';

            return $response->redirect($request->fullRouteByName($loginRoute));
        }

        $request->setAuthenticatedUser($authenticatedUser);
        $request->setRouteContext('auth_guard', $activeGuard);

        if (isset($authenticatedUser->id)) {
            $request->setHeader('X-Account-ID', (string) $authenticatedUser->id);
        }

        return $next($request, $response);
    }
}
