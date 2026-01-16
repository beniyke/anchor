<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for audit logging operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit;

use App\Models\User;
use Audit\Models\AuditLog;
use Audit\Services\AuditManagerService;
use Audit\Services\Builders\LogBuilder;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;

class Audit
{
    /**
     * Start a fluent manual log builder.
     */
    public static function make(): LogBuilder
    {
        return resolve(AuditManagerService::class)->make();
    }

    public static function log(
        string $event,
        array $data = [],
        ?BaseModel $model = null,
        ?User $user = null
    ): AuditLog {
        return resolve(AuditManagerService::class)->log($event, $data, $model, $user);
    }

    public static function logsFor(BaseModel $model): ModelCollection
    {
        return resolve(AuditManagerService::class)->getLogsFor($model);
    }

    public static function logsByUser(User $user): ModelCollection
    {
        return resolve(AuditManagerService::class)->getLogsByUser($user);
    }

    public static function recent(int $limit = 50): ModelCollection
    {
        return resolve(AuditManagerService::class)->getRecent($limit);
    }

    /**
     * Clean up old audit logs.
     */
    public static function cleanup(?int $daysToRetain = null): int
    {
        return resolve(AuditManagerService::class)->cleanup($daysToRetain);
    }

    public static function export(array $filters = [], string $format = 'csv'): string
    {
        return resolve(AuditManagerService::class)->export($filters, $format);
    }

    /**
     * Count events by type and date range.
     */
    public static function countByEvent(string|array $events, ?string $from = null, ?string $to = null): int
    {
        return resolve(AuditManagerService::class)->countByEvent($events, $from, $to);
    }

    /**
     * Generic query for audit logs.
     */
    public static function query(array $filters = []): ModelCollection
    {
        return resolve(AuditManagerService::class)->query($filters);
    }

    public static function queryBuilder(array $filters = []): Builder
    {
        return resolve(AuditManagerService::class)->queryBuilder($filters);
    }

    public static function verify(AuditLog $log): bool
    {
        return resolve(AuditManagerService::class)->verifyChecksum($log);
    }

    /**
     * Forward static calls to AuditManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(AuditManagerService::class)->$method(...$arguments);
    }
}
