<?php

declare(strict_types=1);

namespace Flow\Models;

use Database\BaseModel;

class TaskTag extends BaseModel
{
    public const TABLE = 'flow_task_tag';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'task_id',
        'tag_id',
    ];
}
