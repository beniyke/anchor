<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reminder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $task_id
 * @property int             $user_id
 * @property string          $type
 * @property int             $value
 * @property string          $unit
 * @property string          $status
 * @property ?DateTimeHelper $remind_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Task $task
 * @property-read User $user
 */
class Reminder extends BaseModel
{
    protected string $table = 'flow_reminder';

    protected array $fillable = [
        'task_id',
        'user_id',
        'type',
        'value',
        'unit',
        'status',
        'remind_at'
    ];

    protected array $casts = [
        'value' => 'integer',
        'remind_at' => 'datetime'
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
