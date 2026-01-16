<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Attachment
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $task_id
 * @property string          $path
 * @property string          $filename
 * @property string          $mime_type
 * @property int             $size
 * @property int             $uploaded_by
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Task $task
 * @property-read User $uploader
 */
class Attachment extends BaseModel
{
    use HasRefid;

    protected string $table = 'flow_attachment';

    protected array $fillable = ['refid', 'task_id', 'path', 'filename', 'mime_type', 'size', 'uploaded_by'];

    protected array $casts = ['refid' => 'string'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
