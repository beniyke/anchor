<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * AuditLog model for storing audit trail entries.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property ?int            $user_id
 * @property ?string         $user_ip
 * @property ?string         $user_agent
 * @property string          $event
 * @property ?string         $auditable_type
 * @property ?int            $auditable_id
 * @property ?array          $old_values
 * @property ?array          $new_values
 * @property ?array          $metadata
 * @property ?string         $checksum
 * @property ?DateTimeHelper $created_at
 *
 * @method static Builder event(string $event)
 * @method static Builder forModel(BaseModel $model)
 * @method static Builder byUser(int $userId)
 * @method static Builder recent(int $limit = 50)
 * @method static Builder dateRange(string $from, string $to)
 */
class AuditLog extends BaseModel
{
    protected string $table = 'audit_log';

    public bool $timestamps = false;

    protected array $fillable = [
        'refid',
        'user_id',
        'user_ip',
        'user_agent',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'metadata',
        'checksum',
        'created_at',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'auditable_id' => 'int',
        'old_values' => 'json',
        'new_values' => 'json',
        'metadata' => 'json',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo('auditable', 'auditable_type', 'auditable_id');
    }

    public function getChanges(): array
    {
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];
        $changes = [];

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($allKeys as $key) {
            $oldValue = $old[$key] ?? null;
            $newValue = $new[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    public function isCreate(): bool
    {
        return $this->event === 'created';
    }

    public function isUpdate(): bool
    {
        return $this->event === 'updated';
    }

    public function isDelete(): bool
    {
        return $this->event === 'deleted';
    }

    public function scopeEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    public function scopeForModel(Builder $query, BaseModel $model): Builder
    {
        return $query->where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent(Builder $query, int $limit = 50): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to);
    }
}
