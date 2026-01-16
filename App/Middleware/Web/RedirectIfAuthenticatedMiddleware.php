<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Closure;
use Core\Middleware\MiddlewareInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;

class RedirectIfAuthenticatedMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if ($this->auth->isAuthenticated() && $request->isLoginRoute()) {
            return $response->redirect($request->fullRouteByName('home'));
        }

        return $next($request, $response);
    }
}
