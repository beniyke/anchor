<?php

declare(strict_types=1);

/**
 * This class implements middleware for API authentication. It extends
 * the base API authentication middleware to validate incoming requests
 * by checking the provided token. If the token is invalid, it terminates
 * the request with an unauthorized access response.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Middleware\Api;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Closure;
use Core\Middleware\MiddlewareInterface;
use Helpers\Format\FormatCollection;
use Helpers\Http\Request;
use Helpers\Http\Response;

class ApiAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthServiceInterface $auth
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): Response
    {
        if ($request->routeShouldBypassAuth()) {
            return $next($request, $response);
        }

        if (! $this->auth->isAuthenticated()) {
            return $response
                ->header(['Content-Type' => 'application/json'])
                ->status(401)
                ->body(FormatCollection::asJson([
                    'error' => 'Unauthorized Access',
                ]));
        }

        return $next($request, $response);
    }
}
