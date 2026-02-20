<?php

declare(strict_types=1);

namespace Flow\Models;

use Database\BaseModel;

class TaskAssignee extends BaseModel
{
    public const TABLE = 'flow_task_assignee';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'task_id',
        'user_id',
    ];
}
