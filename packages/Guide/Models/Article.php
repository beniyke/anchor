<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Article.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\BelongsToMany;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $guide_category_id
 * @property string          $title
 * @property string          $slug
 * @property string          $content
 * @property string          $status
 * @property int             $view_count
 * @property bool            $is_featured
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Category $category
 * @property-read ModelCollection $feedback
 * @property-read ModelCollection $relatedArticles
 *
 * @method static Builder published()
 */
class Article extends BaseModel
{
    protected string $table = 'guide_article';

    protected array $fillable = [
        'refid',
        'guide_category_id',
        'title',
        'slug',
        'content',
        'status',
        'view_count',
        'is_featured',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'guide_category_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'guide_article_id');
    }

    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'guide_related_article',
            'guide_article_id',
            'related_article_id'
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
