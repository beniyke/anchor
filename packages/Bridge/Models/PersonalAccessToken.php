<?php

declare(strict_types=1);

namespace Bridge\Models;

use Database\BaseModel;

class PersonalAccessToken extends BaseModel
{
    public const TABLE = 'personal_access_token';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected array $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
