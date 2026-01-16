<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Manages alert checks and distribution to channels.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Alerts;

use Helpers\DateTimeHelper;
use Helpers\File\Contracts\CacheInterface;
use Watcher\Analytics\WatcherAnalytics;
use Watcher\Config\WatcherConfig;

class AlertManager
{
    private WatcherConfig $config;

    private WatcherAnalytics $analytics;

    private CacheInterface $cache;

    private array $channels = [];

    public function __construct(WatcherConfig $config, WatcherAnalytics $analytics, CacheInterface $cache)
    {
        $this->config = $config;
        $this->analytics = $analytics;
        $this->cache = $cache->withPath('watcher_alerts');
    }

    public function registerChannel(string $name, $channel): void
    {
        $this->channels[$name] = $channel;
    }

    public function checkThresholds(): array
    {
        if (! $this->config->areAlertsEnabled()) {
            return [];
        }

        $alerts = [];

        if ($threshold = $this->config->getAlertThreshold('error_rate')) {
            $alert = $this->checkErrorRate($threshold);
            if ($alert) {
                $alerts[] = $alert;
            }
        }

        if ($threshold = $this->config->getAlertThreshold('slow_query')) {
            $alert = $this->checkSlowQueries($threshold);
            if ($alert) {
                $alerts[] = $alert;
            }
        }

        if ($threshold = $this->config->getAlertThreshold('slow_request')) {
            $alert = $this->checkSlowRequests($threshold);
            if ($alert) {
                $alerts[] = $alert;
            }
        }

        // Send alerts
        foreach ($alerts as $alert) {
            $this->sendAlert($alert['type'], $alert['data']);
        }

        return $alerts;
    }

    private function checkErrorRate(float $threshold): ?array
    {
        $stats = $this->analytics->getRequestStats('1h');
        $total = $stats['total_requests'];

        if ($total === 0) {
            return null;
        }

        $errors = $stats['status_codes'][500] ?? 0;
        $errorRate = ($errors / $total) * 100;

        if ($errorRate > $threshold) {
            return [
                'type' => 'error_rate',
                'data' => [
                    'error_rate' => round($errorRate, 2),
                    'threshold' => $threshold,
                    'total_requests' => $total,
                    'errors' => $errors,
                ],
            ];
        }

        return null;
    }

    private function checkSlowQueries(int $thresholdMs): ?array
    {
        $slowQueries = $this->analytics->getSlowQueries(5);
        $count = count($slowQueries);

        if ($count > 0) {
            $slowest = $slowQueries[0] ?? null;
            if ($slowest && $slowest['duration_ms'] > $thresholdMs) {
                return [
                    'type' => 'slow_query',
                    'data' => [
                        'count' => $count,
                        'threshold_ms' => $thresholdMs,
                        'slowest' => $slowest,
                    ],
                ];
            }
        }

        return null;
    }

    private function checkSlowRequests(int $thresholdMs): ?array
    {
        $stats = $this->analytics->getRequestStats('1h');
        $avgTime = $stats['avg_response_time_ms'];

        if ($avgTime > $thresholdMs) {
            return [
                'type' => 'slow_request',
                'data' => [
                    'avg_response_time_ms' => $avgTime,
                    'threshold_ms' => $thresholdMs,
                    'total_requests' => $stats['total_requests'],
                ],
            ];
        }

        return null;
    }

    public function sendAlert(string $type, array $data): void
    {
        $alertKey = $type . '_' . DateTimeHelper::now()->format('Y-m-d-H');

        if ($this->shouldThrottle($alertKey)) {
            return;
        }

        $channels = $this->config->getAlertChannels();

        foreach ($channels as $channelName => $channelConfig) {
            if (! empty($channelConfig) && isset($this->channels[$channelName])) {
                $this->channels[$channelName]->send($type, $data);
            }
        }

        $this->cache->write($alertKey, time(), $this->config->getAlertThrottle());
    }

    public function shouldThrottle(string $alertKey): bool
    {
        return $this->cache->has($alertKey);
    }
}
