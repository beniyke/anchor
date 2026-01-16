<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Comment
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
 * @property int             $user_id
 * @property string          $content
 * @property ?array          $mentions
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Task $task
 * @property-read User $user
 */
class Comment extends BaseModel
{
    use HasRefid;

    protected string $table = 'flow_comment';

    protected array $fillable = ['refid', 'task_id', 'user_id', 'content', 'mentions'];

    protected array $casts = [
        'refid' => 'string',
        'mentions' => 'array'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
