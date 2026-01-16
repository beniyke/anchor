<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Category.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $name
 * @property string          $slug
 * @property ?string         $description
 * @property ?int            $parent_id
 * @property int             $order
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ?Category $parent
 * @property-read ModelCollection $children
 * @property-read ModelCollection $articles
 */
class Category extends BaseModel
{
    protected string $table = 'guide_category';

    protected array $fillable = [
        'refid',
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'guide_category_id');
    }
}
