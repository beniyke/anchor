<?php

declare(strict_types=1);

namespace Blish\Models;

use Database\BaseModel;

class SubscriberTag extends BaseModel
{
    public const TABLE = 'blish_subscriber_tag';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'subscriber_id',
        'tag_id',
    ];
}
