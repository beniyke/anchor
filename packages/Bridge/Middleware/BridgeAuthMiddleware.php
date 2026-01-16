<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * BridgeAuthMiddleware handles authentication for bridge requests using tokens.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Middleware;

use Bridge\TokenManager;
use Closure;
use Core\Middleware\MiddlewareInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;

class BridgeAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly TokenManager $tokenManager
    ) {
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        $token = $request->getBearerToken();

        if (! $token) {
            return $response
                ->json(['error' => 'Unauthenticated'])
                ->status(401);
        }

        $tokenable = $this->tokenManager->authenticate($token, function (string $type, int $id) {
            if (! class_exists($type)) {
                return null;
            }

            return $type::find($id);
        });

        if (! $tokenable) {
            return $response
                ->json(['error' => 'Invalid or expired token'])
                ->status(401);
        }

        $request->setAuthenticatedUser($tokenable);
        $request->setAuthToken($token);

        return $next($request, $response);
    }

    public function checkAbility(Request $request, string $ability): bool
    {
        $token = $request->getBearerToken();

        if (! $token) {
            return false;
        }

        return $this->tokenManager->checkAbility($token, $ability);
    }
}
