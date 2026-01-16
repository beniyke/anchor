<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Middleware for identifying tenant based on request.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Middleware;

use Closure;
use Core\Middleware\MiddlewareInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;
use Tenancy\Exceptions\TenantException;
use Tenancy\TenantManager;
use Throwable;

class TenantIdentificationMiddleware implements MiddlewareInterface
{
    private TenantManager $tenantManager;

    private array $excludedPaths = [];

    public function __construct()
    {
        $this->tenantManager = container()->get(TenantManager::class);
        $this->excludedPaths = config('tenancy.excluded_paths', []);
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        if ($this->isExcludedPath($request->getPath())) {
            return $next($request, $response);
        }

        try {
            $subdomain = $this->extractSubdomain($request);
            $tenant = $this->tenantManager->identifyBySubdomain($subdomain);

            if (!$tenant) {
                $response = $this->handleTenantNotFound($subdomain, $response);

                return $next($request, $response);
            }

            $this->tenantManager->setContext($tenant);

            container()->singleton('tenant', fn () => $tenant);
            container()->singleton('tenant.manager', fn () => $this->tenantManager);

            $response->header(['X-Tenant-ID' => (string) $tenant->id]);

            return $next($request, $response);
        } catch (TenantException $e) {
            $response = $this->handleTenantException($e, $response);

            return $next($request, $response);
        } catch (Throwable $e) {
            logger('tenant.log')->error('Tenant identification failed', [
                'error' => $e->getMessage(),
                'host' => $request->getHost(),
                'path' => $request->getPath(),
            ]);

            $response = $response->status(503)
                ->header([
                    'Content-Type' => 'application/json'
                ])
                ->body('Service temporarily unavailable');

            return $next($request, $response);
        }
    }

    /**
     * Extract subdomain from request with security checks
     */
    private function extractSubdomain(Request $request): ?string
    {
        $host = $request->getHost();

        if (!$this->isValidHost($host)) {
            throw new TenantException('Invalid host format');
        }

        $host = explode(':', $host)[0];
        $parts = explode('.', $host);

        // If localhost or IP, no subdomain
        if ($this->isLocalhost($host) || $this->isIpAddress($host)) {
            return config('tenancy.default_subdomain');
        }

        // Need at least 3 parts for subdomain (subdomain.domain.tld)
        if (count($parts) < 3) {
            return null;
        }

        return $parts[0];
    }

    private function handleTenantNotFound(?string $subdomain, Response $response): Response
    {
        logger('tenant.log')->warning('Tenant not found', ['subdomain' => $subdomain]);

        return $response->status(404)
            ->header([
                'Content-Type' => 'application/json'
            ])
            ->body(json_encode([
                'error' => 'Tenant not found',
                'subdomain' => $subdomain,
            ]));
    }

    private function handleTenantException(TenantException $e, Response $response): Response
    {
        logger('tenant.log')->warning('Tenant error', [
            'error' => $e->getMessage(),
        ]);

        $statusCode = match (true) {
            str_contains($e->getMessage(), 'suspended') => 403,
            str_contains($e->getMessage(), 'expired') => 402,
            str_contains($e->getMessage(), 'not found') => 404,
            default => 400,
        };

        return $response->status($statusCode)
            ->header([
                'Content-Type' => 'application/json'
            ])
            ->body(json_encode([
                'error' => $e->getMessage(),
            ]));
    }

    private function isExcludedPath(string $path): bool
    {
        foreach ($this->excludedPaths as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate host format for security
     */
    private function isValidHost(string $host): bool
    {
        // Remove port
        $host = explode(':', $host)[0];

        // Check for valid hostname or IP
        return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false
            || filter_var($host, FILTER_VALIDATE_IP) !== false;
    }

    private function isLocalhost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '::1']);
    }

    private function isIpAddress(string $host): bool
    {
        return filter_var($host, FILTER_VALIDATE_IP) !== false;
    }
}
