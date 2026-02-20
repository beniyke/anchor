<?php

declare(strict_types=1);

namespace Guide\Models;

use Database\BaseModel;

class ArticleMedia extends BaseModel
{
    public const TABLE = 'guide_article_media';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'guide_article_id',
        'media_id',
        'type',
    ];
}
