<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ExportHistory model to track export jobs.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Export\Enums\ExportFormat;
use Export\Enums\ExportStatus;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $user_id
 * @property string          $exporter_class
 * @property ExportFormat    $format
 * @property string          $filename
 * @property string          $disk
 * @property ?string         $path
 * @property ExportStatus    $status
 * @property ?string         $error
 * @property ?int            $rows_count
 * @property ?int            $file_size
 * @property ?DateTimeHelper $started_at
 * @property ?DateTimeHelper $completed_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 */
class ExportHistory extends BaseModel
{
    protected string $table = 'export_history';

    protected array $fillable = [
        'refid',
        'user_id',
        'exporter_class',
        'format',
        'filename',
        'disk',
        'path',
        'status',
        'error',
        'rows_count',
        'file_size',
        'started_at',
        'completed_at',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'format' => ExportFormat::class,
        'status' => ExportStatus::class,
        'rows_count' => 'int',
        'file_size' => 'int',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isPending(): bool
    {
        return $this->status === ExportStatus::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === ExportStatus::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === ExportStatus::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === ExportStatus::FAILED;
    }

    /**
     * Mark export as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => ExportStatus::PROCESSING,
            'started_at' => DateTimeHelper::now(),
        ]);
    }

    /**
     * Mark export as completed.
     */
    public function markAsCompleted(string $path, int $rowsCount, int $fileSize): void
    {
        $this->update([
            'status' => ExportStatus::COMPLETED,
            'path' => $path,
            'rows_count' => $rowsCount,
            'file_size' => $fileSize,
            'completed_at' => DateTimeHelper::now(),
        ]);
    }

    /**
     * Mark export as failed.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => ExportStatus::FAILED,
            'error' => $error,
            'completed_at' => DateTimeHelper::now(),
        ]);
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    /**
     * Scope for user's exports.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', ExportStatus::COMPLETED);
    }

    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
