<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Aggregates statistics for requests, queries, exceptions, and jobs.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Analytics;

use Watcher\Storage\WatcherRepository;

class WatcherAnalytics
{
    private WatcherRepository $repository;

    public function __construct(WatcherRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRequestStats(string $period = '24h'): array
    {
        $since = $this->getPeriodStart($period);
        $stats = $this->repository->getStats('request', $since);

        $totalRequests = $stats['count'];
        $avgResponseTime = 0;
        $statusCodes = [];

        foreach ($stats['entries'] as $entry) {
            $data = json_decode($entry['content'], true);
            $avgResponseTime += $data['duration_ms'] ?? 0;
            $status = $data['status'] ?? 200;
            $statusCodes[$status] = ($statusCodes[$status] ?? 0) + 1;
        }

        if ($totalRequests > 0) {
            $avgResponseTime = round($avgResponseTime / $totalRequests, 2);
        }

        return [
            'total_requests' => $totalRequests,
            'avg_response_time_ms' => $avgResponseTime,
            'status_codes' => $statusCodes,
            'period' => $period,
        ];
    }

    public function getQueryStats(string $period = '24h'): array
    {
        $since = $this->getPeriodStart($period);
        $stats = $this->repository->getStats('query', $since);

        $totalQueries = $stats['count'];
        $avgDuration = 0;
        $slowQueries = [];

        foreach ($stats['entries'] as $entry) {
            $data = json_decode($entry['content'], true);
            $duration = $data['time_ms'] ?? 0;
            $avgDuration += $duration;

            if ($duration > 100) { // Slow query threshold
                $slowQueries[] = [
                    'sql' => $data['sql'] ?? '',
                    'duration_ms' => $duration,
                    'created_at' => $entry['created_at'],
                ];
            }
        }

        if ($totalQueries > 0) {
            $avgDuration = round($avgDuration / $totalQueries, 2);
        }

        return [
            'total_queries' => $totalQueries,
            'avg_duration_ms' => $avgDuration,
            'slow_queries_count' => count($slowQueries),
            'period' => $period,
        ];
    }

    public function getExceptionStats(string $period = '24h'): array
    {
        $since = $this->getPeriodStart($period);
        $stats = $this->repository->getStats('exception', $since);

        $exceptionsByClass = [];

        foreach ($stats['entries'] as $entry) {
            $data = json_decode($entry['content'], true);
            $class = $data['class'] ?? 'Unknown';
            $exceptionsByClass[$class] = ($exceptionsByClass[$class] ?? 0) + 1;
        }

        return [
            'total_exceptions' => $stats['count'],
            'by_class' => $exceptionsByClass,
            'period' => $period,
        ];
    }

    public function getJobStats(string $period = '24h'): array
    {
        $since = $this->getPeriodStart($period);
        $stats = $this->repository->getStats('job', $since);

        $completed = 0;
        $failed = 0;
        $avgDuration = 0;

        foreach ($stats['entries'] as $entry) {
            $data = json_decode($entry['content'], true);
            $status = $data['status'] ?? 'unknown';

            if ($status === 'completed') {
                $completed++;
            } elseif ($status === 'failed') {
                $failed++;
            }

            $avgDuration += $data['duration_ms'] ?? 0;
        }

        $total = $stats['count'];
        if ($total > 0) {
            $avgDuration = round($avgDuration / $total, 2);
        }

        return [
            'total_jobs' => $total,
            'completed' => $completed,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'avg_duration_ms' => $avgDuration,
            'period' => $period,
        ];
    }

    public function getSlowQueries(int $limit = 10): array
    {
        $stats = $this->repository->getStats('query', $this->getPeriodStart('7d'));
        $slowQueries = [];

        foreach ($stats['entries'] as $entry) {
            $data = json_decode($entry['content'], true);
            $duration = $data['time_ms'] ?? 0;

            if ($duration > 100) {
                $slowQueries[] = [
                    'sql' => $data['sql'] ?? '',
                    'duration_ms' => $duration,
                    'connection' => $data['connection'] ?? 'default',
                    'created_at' => $entry['created_at'],
                ];
            }
        }

        // Sort by duration descending
        usort($slowQueries, fn ($a, $b) => $b['duration_ms'] <=> $a['duration_ms']);

        return array_slice($slowQueries, 0, $limit);
    }

    public function getErrorTrends(string $period = '7d'): array
    {
        $since = $this->getPeriodStart($period);
        $stats = $this->repository->getStats('exception', $since);

        $trends = [];

        foreach ($stats['entries'] as $entry) {
            $date = substr($entry['created_at'], 0, 10); // YYYY-MM-DD
            $trends[$date] = ($trends[$date] ?? 0) + 1;
        }

        ksort($trends);

        return $trends;
    }

    public function getPerformanceMetrics(string $period = '24h'): array
    {
        return [
            'requests' => $this->getRequestStats($period),
            'queries' => $this->getQueryStats($period),
            'exceptions' => $this->getExceptionStats($period),
            'jobs' => $this->getJobStats($period),
        ];
    }

    private function getPeriodStart(string $period): string
    {
        $intervals = [
            '1h' => '-1 hour',
            '24h' => '-24 hours',
            '7d' => '-7 days',
            '30d' => '-30 days',
        ];

        $interval = $intervals[$period] ?? '-24 hours';

        return date('Y-m-d H:i:s', strtotime($interval));
    }
}
