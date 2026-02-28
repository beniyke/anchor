<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use Closure;
use Core\Contracts\AuthServiceInterface;
use Core\Middleware\MiddlewareInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;

class RedirectIfAuthenticatedMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if (! $request->isLoginRoute()) {
            return $next($request, $response);
        }

        $guards = $request->getRouteContext('guards') ?? ['web'];

        foreach ($guards as $guard) {
            if ($this->auth->viaGuard($guard)->isAuthenticated()) {
                $homeRoute = $this->config->get("auth.guards.{$guard}.route.home");

                return $response->redirect($request->baseUrl($homeRoute));
            }
        }

        return $next($request, $response);
    }
}
