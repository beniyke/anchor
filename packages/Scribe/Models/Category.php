<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Category.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Models;

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
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ?Category $parent
 * @property-read ModelCollection $children
 * @property-read ModelCollection $posts
 */
class Category extends BaseModel
{
    protected string $table = 'scribe_category';

    protected array $fillable = [
        'refid',
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    protected array $casts = [
        'parent_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'scribe_category_id');
    }
}
