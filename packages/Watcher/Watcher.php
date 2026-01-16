<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Watcher Facade Provides a clean static interface for the monitoring system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher;

use Watcher\Alerts\AlertManager;
use Watcher\Analytics\WatcherAnalytics;

class Watcher
{
    public static function record(string $type, array $data): void
    {
        resolve(WatcherManager::class)->record($type, $data);
    }

    public static function analytics(): WatcherAnalytics
    {
        return resolve(WatcherAnalytics::class);
    }

    public static function alerts(): AlertManager
    {
        return resolve(AlertManager::class);
    }

    /**
     * Start a new recording batch.
     */
    public static function startBatch(?string $batchId = null): void
    {
        resolve(WatcherManager::class)->startBatch($batchId);
    }

    /**
     * Stop the current batch and flush entries.
     */
    public static function stopBatch(): void
    {
        resolve(WatcherManager::class)->stopBatch();
    }

    /**
     * Flush recorded entries to storage.
     */
    public static function flush(): void
    {
        resolve(WatcherManager::class)->flush();
    }
}
