<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core audit manager service.
 * Handles logging, retrieval, cleanup, and verification of audit logs.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Services;

use App\Models\User;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use Audit\Models\AuditLog;
use Audit\Services\Builders\LogBuilder;
use Core\Services\ConfigServiceInterface;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Helpers\DateTimeHelper;
use Helpers\String\Str;

class AuditManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly AuthServiceInterface $auth
    ) {
    }

    public function make(): LogBuilder
    {
        return new LogBuilder($this);
    }

    public function log(
        string $event,
        array $data = [],
        ?BaseModel $model = null,
        ?User $user = null
    ): AuditLog {
        if (!$this->isEnabled()) {
            return new AuditLog();
        }

        $user = $user ?? $this->auth->user();

        $logData = [
            'refid' => Str::random('secure'),
            'user_id' => $user?->id,
            'user_ip' => $this->getClientIp(),
            'user_agent' => $this->getUserAgent(),
            'event' => $event,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model?->id,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ];

        if ($this->config->get('audit.checksum.enabled', true)) {
            $logData['checksum'] = $this->generateChecksum($logData);
        }

        return AuditLog::create($logData);
    }

    public function logModelEvent(
        BaseModel $model,
        string $event,
        array $oldValues = [],
        array $newValues = [],
        ?User $user = null
    ): AuditLog {
        // Filter out excluded attributes
        $excludedAttributes = $this->config->get('audit.excluded_attributes', []);
        $oldValues = array_diff_key($oldValues, array_flip($excludedAttributes));
        $newValues = array_diff_key($newValues, array_flip($excludedAttributes));

        return $this->log($event, [
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
        ], $model, $user);
    }

    public function getLogsFor(BaseModel $model): ModelCollection
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLogsByUser(User $user): ModelCollection
    {
        return AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecent(int $limit = 50): ModelCollection
    {
        return AuditLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByEvent(string $event, int $limit = 100): ModelCollection
    {
        return AuditLog::where('event', $event)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count events by name and optional date range.
     */
    public function countByEvent(string|array $events, ?string $from = null, ?string $to = null): int
    {
        $query = AuditLog::query();

        if (is_array($events)) {
            $query->whereIn('event', $events);
        } else {
            $query->where('event', $events);
        }

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<', $to);
        }

        return $query->count();
    }

    /**
     * Generic query method for audit logs.
     */
    public function query(array $filters = []): ModelCollection
    {
        return $this->queryBuilder($filters)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Alias for queryBuilder.
     */
    public function history(array $filters = []): \Database\Query\Builder
    {
        return $this->queryBuilder($filters);
    }

    public function analytics(): AuditAnalyticsService
    {
        return new AuditAnalyticsService();
    }

    public function queryBuilder(array $filters = []): \Database\Query\Builder
    {
        $query = AuditLog::query();

        if (isset($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (isset($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        if (isset($filters['metadata_has'])) {
            $query->whereNotNull('metadata');
        }

        return $query;
    }

    public function cleanup(?int $daysToRetain = null): int
    {
        $days = $daysToRetain ?? $this->config->get('audit.retention_days', 90);

        if ($days <= 0) {
            return 0;
        }

        $cutoffDate = DateTimeHelper::now()->subDays($days);

        $count = AuditLog::where('created_at', '<', $cutoffDate)->count();

        AuditLog::where('created_at', '<', $cutoffDate)->delete();

        return $count;
    }

    public function export(array $filters = [], string $format = 'csv'): string
    {
        $query = AuditLog::query();

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            return $this->toCsv($logs);
        }

        return json_encode($logs->toArray(), JSON_PRETTY_PRINT);
    }

    public function verifyChecksum(AuditLog $log): bool
    {
        if (!$this->config->get('audit.checksum.enabled', true)) {
            return true;
        }

        $data = [
            'refid' => $log->refid,
            'user_id' => $log->user_id,
            'user_ip' => $log->user_ip,
            'user_agent' => $log->user_agent,
            'event' => $log->event,
            'auditable_type' => $log->auditable_type,
            'auditable_id' => $log->auditable_id,
            'old_values' => $log->old_values,
            'new_values' => $log->new_values,
            'metadata' => $log->metadata,
        ];

        $expectedChecksum = $this->generateChecksum($data);

        return hash_equals($log->checksum ?? '', $expectedChecksum);
    }

    public function isEnabled(): bool
    {
        return $this->config->get('audit.enabled', true);
    }

    private function generateChecksum(array $data): string
    {
        unset($data['checksum']);
        $algorithm = $this->config->get('audit.checksum.algorithm', 'sha256');
        $payload = json_encode($data);
        $key = $this->config->get('encryption_key', 'audit-secret-key');

        return hash_hmac($algorithm, $payload, $key);
    }

    private function getClientIp(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    }

    private function getUserAgent(): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    private function toCsv(ModelCollection $logs): string
    {
        $csv = "id,refid,user_id,event,auditable_type,auditable_id,created_at\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->refid,
                $log->user_id ?? '',
                $log->event,
                $log->auditable_type ?? '',
                $log->auditable_id ?? '',
                $log->created_at
            );
        }

        return $csv;
    }
}
