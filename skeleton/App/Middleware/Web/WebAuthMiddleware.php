<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Closure;
use Core\Middleware\MiddlewareInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;

class WebAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $auth
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if ($request->routeShouldBypassAuth()) {
            return $next($request, $response);
        }

        if (! $this->auth->isAuthenticated() || ! $this->auth->isAuthorized($request->route())) {
            $this->auth->logout();

            return $response->redirect($request->fullRouteByName('login'));
        }

        if ($user = $this->auth->user()) {
            $request->setHeader('X-Account-ID', (string) $user->id);
        }

        return $next($request, $response);
    }
}
