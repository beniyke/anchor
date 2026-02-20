<?php

declare(strict_types=1);

namespace Guide\Models;

use Database\BaseModel;

class RelatedArticle extends BaseModel
{
    public const TABLE = 'guide_related_article';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'guide_article_id',
        'related_article_id',
    ];
}
