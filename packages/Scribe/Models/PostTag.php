<?php

declare(strict_types=1);

namespace Scribe\Models;

use Database\BaseModel;

class PostTag extends BaseModel
{
    public const TABLE = 'scribe_post_tag';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'post_id',
        'tag_id',
    ];
}
