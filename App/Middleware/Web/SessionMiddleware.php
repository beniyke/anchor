<?php

declare(strict_types=1);

namespace App\Middleware\Web;

use Closure;
use Core\Middleware\MiddlewareInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;
use Helpers\Http\Session;

class SessionMiddleware implements MiddlewareInterface
{
    private readonly Session $session;

    private readonly ConfigServiceInterface $config;

    public function __construct(Session $session, ConfigServiceInterface $config)
    {
        $this->session = $session;
        $this->config = $config;
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if ($this->config->get('session.regenerate')) {
            $this->session->periodicRegenerate();
        }

        return $next($request, $response);
    }
}
