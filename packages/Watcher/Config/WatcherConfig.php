<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Configuration holder for the Watcher system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Config;

class WatcherConfig
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_replace_recursive($this->defaults(), $config);
    }

    private function defaults(): array
    {
        return [
            'enabled' => true,
            'types' => [
                'request' => true,
                'query' => true,
                'exception' => true,
                'job' => true,
                'log' => true,
                'cache' => false,
                'mail' => false,
                'ghost' => true,
            ],
            'sampling' => [
                'request' => 1.0, // 100%
                'query' => 1.0,
                'exception' => 1.0, // Always record exceptions
                'job' => 1.0,
                'log' => 0.1, // 10% of logs
                'cache' => 0.1,
                'mail' => 1.0,
                'ghost' => 1.0,
            ],
            'batch' => [
                'enabled' => true,
                'size' => 50,
                'flush_interval' => 5, // seconds
            ],
            'retention' => [
                'request' => 7, // days
                'query' => 7,
                'exception' => 30,
                'job' => 14,
                'log' => 7,
                'cache' => 1,
                'mail' => 7,
                'ghost' => 30,
            ],
            'filters' => [
                'ignore_paths' => [
                    '/health',
                    '/metrics',
                    '/favicon.ico',
                ],
                'ignore_queries' => [
                    'SELECT 1', // Health checks
                ],
                'redact_fields' => [
                    'password',
                    'token',
                    'secret',
                    'api_key',
                    'credit_card',
                ],
            ],
            'alerts' => [
                'enabled' => false,
                'thresholds' => [
                    'error_rate' => 5.0, // percent
                    'slow_query' => 1000, // ms
                    'slow_request' => 2000, // ms
                ],
                'channels' => [
                    'email' => [],
                    'slack' => null,
                    'webhook' => null,
                ],
                'throttle' => 300, // seconds between same alert
            ],
        ];
    }

    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }

    public function isTypeEnabled(string $type): bool
    {
        return $this->config['types'][$type] ?? false;
    }

    public function getSamplingRate(string $type): float
    {
        return $this->config['sampling'][$type] ?? 1.0;
    }

    public function getBatchSize(): int
    {
        return $this->config['batch']['size'];
    }

    public function getBatchFlushInterval(): int
    {
        return $this->config['batch']['flush_interval'];
    }

    public function isBatchingEnabled(): bool
    {
        return $this->config['batch']['enabled'];
    }

    public function getRetentionDays(string $type): int
    {
        return $this->config['retention'][$type] ?? 7;
    }

    public function getIgnoredPaths(): array
    {
        return $this->config['filters']['ignore_paths'];
    }

    public function getIgnoredQueries(): array
    {
        return $this->config['filters']['ignore_queries'];
    }

    public function getRedactFields(): array
    {
        return $this->config['filters']['redact_fields'];
    }

    public function areAlertsEnabled(): bool
    {
        return $this->config['alerts']['enabled'];
    }

    public function getAlertThreshold(string $metric): float|int|null
    {
        return $this->config['alerts']['thresholds'][$metric] ?? null;
    }

    public function getAlertChannels(): array
    {
        return $this->config['alerts']['channels'];
    }

    public function getAlertThrottle(): int
    {
        return $this->config['alerts']['throttle'];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}
