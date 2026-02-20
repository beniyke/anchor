<?php

declare(strict_types=1);

namespace Watcher\Models;

use Database\BaseModel;

class WatcherEntry extends BaseModel
{
    public const TABLE = 'watcher_entry';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'batch_id',
        'type',
        'family_hash',
        'content',
    ];

    protected array $casts = [
        'content' => 'array',
    ];
}
