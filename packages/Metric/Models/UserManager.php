<?php

declare(strict_types=1);

namespace Metric\Models;

use Database\BaseModel;

class UserManager extends BaseModel
{
    public const TABLE = 'metric_user_manager';

    protected string $table = self::TABLE;
}
