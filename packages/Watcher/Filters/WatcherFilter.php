<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Filters sensitive data and ignores specific paths/queries.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Filters;

use Watcher\Config\WatcherConfig;

class WatcherFilter
{
    private WatcherConfig $config;

    public function __construct(WatcherConfig $config)
    {
        $this->config = $config;
    }

    public function shouldIgnore(string $type, array $data): bool
    {
        return match ($type) {
            'request' => $this->shouldIgnoreRequest($data),
            'query' => $this->shouldIgnoreQuery($data),
            default => false,
        };
    }

    public function filter(string $type, array $data): array
    {
        return $this->redactSensitiveData($data);
    }

    private function shouldIgnoreRequest(array $data): bool
    {
        $uri = $data['uri'] ?? '';
        $ignoredPaths = $this->config->getIgnoredPaths();

        foreach ($ignoredPaths as $path) {
            if (str_starts_with($uri, $path)) {
                return true;
            }
        }

        return false;
    }

    private function shouldIgnoreQuery(array $data): bool
    {
        $sql = trim($data['sql'] ?? '');
        $ignoredQueries = $this->config->getIgnoredQueries();

        foreach ($ignoredQueries as $ignoredQuery) {
            if (str_starts_with($sql, $ignoredQuery)) {
                return true;
            }
        }

        return false;
    }

    private function redactSensitiveData(array $data): array
    {
        $redactFields = $this->config->getRedactFields();

        array_walk_recursive($data, function (&$value, $key) use ($redactFields) {
            if (in_array(strtolower($key), $redactFields)) {
                $value = '[REDACTED]';
            }
        });

        return $data;
    }
}
