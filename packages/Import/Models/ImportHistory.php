<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ImportHistory model to track import jobs.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;
use Import\Enums\ImportStatus;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $user_id
 * @property string          $importer_class
 * @property string          $filename
 * @property string          $original_filename
 * @property string          $disk
 * @property ?string         $path
 * @property ImportStatus    $status
 * @property ?string         $error
 * @property int             $total_rows
 * @property int             $processed_rows
 * @property int             $success_rows
 * @property int             $failed_rows
 * @property int             $skipped_rows
 * @property ?DateTimeHelper $started_at
 * @property ?DateTimeHelper $completed_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read ModelCollection $errors
 */
class ImportHistory extends BaseModel
{
    protected string $table = 'import_history';

    protected array $fillable = [
        'refid',
        'user_id',
        'importer_class',
        'filename',
        'original_filename',
        'disk',
        'path',
        'status',
        'error',
        'total_rows',
        'processed_rows',
        'success_rows',
        'failed_rows',
        'skipped_rows',
        'started_at',
        'completed_at',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'status' => ImportStatus::class,
        'total_rows' => 'int',
        'processed_rows' => 'int',
        'success_rows' => 'int',
        'failed_rows' => 'int',
        'skipped_rows' => 'int',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function errors(): HasMany
    {
        return $this->hasMany(ImportError::class, 'import_id');
    }

    /**
     * Mark import as processing.
     */
    public function markAsProcessing(int $totalRows = 0): void
    {
        $this->update([
            'status' => ImportStatus::PROCESSING,
            'total_rows' => $totalRows,
            'started_at' => DateTimeHelper::now(),
        ]);
    }

    /**
     * Mark import as completed.
     */
    public function markAsCompleted(): void
    {
        $status = $this->failed_rows > 0 ? ImportStatus::PARTIAL : ImportStatus::COMPLETED;

        $this->update([
            'status' => $status,
            'completed_at' => DateTimeHelper::now(),
        ]);
    }

    /**
     * Mark import as failed.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => ImportStatus::FAILED,
            'error' => $error,
            'completed_at' => DateTimeHelper::now(),
        ]);
    }

    /**
     * Increment progress counters.
     */
    public function incrementProgress(string $type): void
    {
        $this->increment('processed_rows');
        $this->increment($type . '_rows');
    }

    public function isPending(): bool
    {
        return $this->status === ImportStatus::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === ImportStatus::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [ImportStatus::COMPLETED, ImportStatus::PARTIAL]);
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }
}
