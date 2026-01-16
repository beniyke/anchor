<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Policy for retaining and cleaning up Watcher entries.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Retention;

use Watcher\Config\WatcherConfig;
use Watcher\Storage\WatcherRepository;

class RetentionPolicy
{
    private WatcherConfig $config;

    private WatcherRepository $repository;

    public function __construct(WatcherConfig $config, WatcherRepository $repository)
    {
        $this->config = $config;
        $this->repository = $repository;
    }

    public function cleanup(): array
    {
        $results = [];
        $types = ['request', 'query', 'exception', 'job', 'log', 'cache', 'mail'];

        foreach ($types as $type) {
            $deleted = $this->cleanupType($type);
            if ($deleted > 0) {
                $results[$type] = $deleted;
            }
        }

        return $results;
    }

    public function cleanupType(string $type): int
    {
        $retentionDays = $this->config->getRetentionDays($type);

        if ($retentionDays <= 0) {
            return 0; // Retention disabled for this type
        }

        return $this->repository->deleteOlderThan($type, $retentionDays);
    }

    public function getStats(): array
    {
        $stats = [];
        $types = ['request', 'query', 'exception', 'job', 'log', 'cache', 'mail'];

        foreach ($types as $type) {
            $retentionDays = $this->config->getRetentionDays($type);
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$retentionDays} days"));

            $total = $this->repository->countByType($type);
            $eligible = $this->repository->countByType($type, null) -
                $this->repository->countByType($type, $cutoffDate);

            $stats[$type] = [
                'retention_days' => $retentionDays,
                'total_entries' => $total,
                'eligible_for_cleanup' => max(0, $eligible),
            ];
        }

        return $stats;
    }
}
