<?php

declare(strict_types=1);

namespace Blish\Models;

use Database\BaseModel;

class ListSubscriber extends BaseModel
{
    public const TABLE = 'blish_list_subscriber';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'subscriber_id',
        'list_id',
    ];
}
