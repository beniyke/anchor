<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Dependency Pivot Model
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use Database\BaseModel;

class Dependency extends BaseModel
{
    public const TABLE = 'flow_dependency';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'task_id',
        'depends_on_task_id',
    ];
}
