<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Middleware for recording requests in the Watcher system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Middleware;

use Closure;
use Core\Middleware\MiddlewareInterface;
use Helpers\Http\Request;
use Helpers\Http\Response;
use Helpers\String\UuidGenerator;
use Watcher\WatcherManager;

class WatcherMiddleware implements MiddlewareInterface
{
    private WatcherManager $watcher;

    public function __construct(WatcherManager $watcher)
    {
        $this->watcher = $watcher;
    }

    public function handle(Request $request, Response $response, Closure $next): mixed
    {
        $startTime = microtime(true);
        $batchId = $this->generateBatchId();

        $this->watcher->startBatch($batchId);
        $this->recordRequest($request, $response, $startTime, $batchId);
        $this->watcher->stopBatch();

        return $next($request, $response);
    }

    private function recordRequest(Request $request, Response $response, float $startTime, string $batchId): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $requestData = [
            'method' => $request->method(),
            'uri' => $request->uri(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'ip' => $request->ip(),
            'user_agent' => $request->header('user-agent'),
            'batch_id' => $batchId,
        ];

        $this->watcher->record('request', $requestData);
    }

    private function generateBatchId(): string
    {
        return UuidGenerator::v4();
    }
}
