<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for import operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import;

use Import\Contracts\Importable;
use Import\Models\ImportHistory;
use Import\Services\Builders\ImportBuilder;
use Import\Services\ImportAnalyticsService;
use Import\Services\ImportManagerService;

class Import
{
    /**
     * Create a new import builder.
     */
    public static function make(string|Importable $importer): ImportBuilder
    {
        return resolve(ImportManagerService::class)->make($importer);
    }

    /**
     * Queue an import for background processing.
     */
    public static function queue(string|Importable $importer, string $filePath, array $options = []): ImportHistory
    {
        return resolve(ImportManagerService::class)->queue($importer, $filePath, $options);
    }

    public static function history(?int $userId = null): array
    {
        return resolve(ImportManagerService::class)->getHistory($userId);
    }

    /**
     * Find an import by ID.
     */
    public static function find(int $id): ?ImportHistory
    {
        return resolve(ImportManagerService::class)->find($id);
    }

    public static function findByRefid(string $refid): ?ImportHistory
    {
        return resolve(ImportManagerService::class)->findByRefid($refid);
    }

    public static function analytics(): ImportAnalyticsService
    {
        return resolve(ImportAnalyticsService::class);
    }

    /**
     * Forward static calls to ImportManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(ImportManagerService::class)->$method(...$arguments);
    }
}
