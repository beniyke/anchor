<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * HasAuditTrail trait for automatic model event logging.
 * Add to any model to automatically log create, update, delete, and restore events.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Traits;

use Audit\Services\AuditManagerService;
use Database\Collections\ModelCollection;

trait HasAuditTrail
{
    /**
     * Boot the trait and register model event listeners.
     */
    public static function bootHasAuditTrail(): void
    {
        // Log created events
        if (config('audit.events.created', true)) {
            static::created(function ($model) {
                $model->logAuditEvent('created', [], $model->getAttributes());
            });
        }

        // Log updated events
        if (config('audit.events.updated', true)) {
            static::updated(function ($model) {
                $model->logAuditEvent('updated', $model->getOriginal(), $model->getAttributes());
            });
        }

        // Log deleted events
        if (config('audit.events.deleted', true)) {
            static::deleted(function ($model) {
                $model->logAuditEvent('deleted', $model->getAttributes(), []);
            });
        }
    }

    /**
     * Log an audit event for this model.
     */
    public function logAuditEvent(string $event, array $oldValues, array $newValues): void
    {
        if (!config('audit.enabled', true)) {
            return;
        }

        // Filter out excluded attributes
        $excludedAttributes = array_merge(
            config('audit.excluded_attributes', []),
            $this->auditExclude ?? []
        );

        $oldValues = array_diff_key($oldValues, array_flip($excludedAttributes));
        $newValues = array_diff_key($newValues, array_flip($excludedAttributes));

        // Only log if there are actual changes for updates
        if ($event === 'updated' && empty($this->getAuditDiff($oldValues, $newValues))) {
            return;
        }

        resolve(AuditManagerService::class)->logModelEvent(
            $this,
            $event,
            $oldValues,
            $newValues
        );
    }

    public function auditLogs(): ModelCollection
    {
        return resolve(AuditManagerService::class)->getLogsFor($this);
    }

    public function lastAuditLog(): ?object
    {
        return $this->auditLogs()->first();
    }

    /**
     * Get attributes that have changed between old and new values.
     */
    protected function getAuditDiff(array $oldValues, array $newValues): array
    {
        $diff = [];

        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $diff[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $diff;
    }

    public function getAuditExclude(): array
    {
        return $this->auditExclude ?? [];
    }

    /**
     * Set attributes to exclude from audit logging.
     */
    public function setAuditExclude(array $attributes): void
    {
        $this->auditExclude = $attributes;
    }
}
