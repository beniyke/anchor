<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Task Completion.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $user_id
 * @property int             $onboard_task_id
 * @property ?DateTimeHelper $completed_at
 * @property ?string         $notes
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read Task $task
 */
class TaskCompletion extends BaseModel
{
    protected string $table = 'onboard_task_completion';

    protected array $fillable = [
        'user_id',
        'onboard_task_id',
        'completed_at',
        'notes',
    ];

    protected array $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'onboard_task_id');
    }
}
