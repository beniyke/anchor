<?php

declare(strict_types=1);

namespace Workflow\Models;

use Database\BaseModel;

class WorkflowHistory extends BaseModel
{
    public const TABLE = 'workflow_history';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'instance_id',
        'type',
        'payload',
        'result',
        'workflow_class',
        'input',
    ];

    protected array $casts = [
        'payload' => 'array',
        'result' => 'array',
        'input' => 'array',
    ];
}
