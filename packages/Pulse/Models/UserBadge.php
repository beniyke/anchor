<?php

declare(strict_types=1);

namespace Pulse\Models;

use Database\BaseModel;

class UserBadge extends BaseModel
{
    public const TABLE = 'pulse_user_badge';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'user_id',
        'badge_id',
    ];
}
