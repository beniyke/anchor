<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for export operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export;

use Export\Contracts\Exportable;
use Export\Models\ExportHistory;
use Export\Services\Builders\ExportBuilder;
use Export\Services\ExportAnalyticsService;
use Export\Services\ExportManagerService;

class Export
{
    /**
     * Create a new export builder.
     */
    public static function make(string|Exportable $exporter): ExportBuilder
    {
        return resolve(ExportManagerService::class)->make($exporter);
    }

    /**
     * Queue an export for background processing.
     */
    public static function queue(string|Exportable $exporter, array $options = []): ExportHistory
    {
        return resolve(ExportManagerService::class)->queue($exporter, $options);
    }

    public static function history(?int $userId = null): array
    {
        return resolve(ExportManagerService::class)->getHistory($userId);
    }

    /**
     * Find an export by ID.
     */
    public static function find(int $id): ?ExportHistory
    {
        return resolve(ExportManagerService::class)->find($id);
    }

    public static function findByRefid(string $refid): ?ExportHistory
    {
        return resolve(ExportManagerService::class)->findByRefid($refid);
    }

    public static function download(ExportHistory $export): array
    {
        return resolve(ExportManagerService::class)->download($export);
    }

    /**
     * Clean up old exports.
     */
    public static function cleanup(?int $daysToRetain = null): int
    {
        return resolve(ExportManagerService::class)->cleanup($daysToRetain);
    }

    public static function analytics(): ExportAnalyticsService
    {
        return resolve(ExportAnalyticsService::class);
    }

    /**
     * Forward static calls to ExportManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(ExportManagerService::class)->$method(...$arguments);
    }
}
