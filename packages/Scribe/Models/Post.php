<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Post.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\BelongsToMany;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $title
 * @property string          $slug
 * @property string          $content
 * @property ?string         $excerpt
 * @property string          $status
 * @property ?DateTimeHelper $published_at
 * @property int             $scribe_category_id
 * @property int             $user_id
 * @property ?array          $seo_meta
 * @property ?array          $settings
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Category $category
 * @property-read ModelCollection $tags
 * @property-read ModelCollection $comments
 */
class Post extends BaseModel
{
    use HasRefid;

    protected string $table = 'scribe_post';

    protected string $refidPrefix = 'pst_';

    protected array $fillable = [
        'refid',
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'published_at',
        'scribe_category_id',
        'user_id',
        'seo_meta',
        'settings',
    ];

    protected array $casts = [
        'published_at' => 'datetime',
        'scribe_category_id' => 'int',
        'user_id' => 'int',
        'seo_meta' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'scribe_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'scribe_post_tag', 'scribe_post_id', 'scribe_tag_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'scribe_post_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && ($this->published_at === null || $this->published_at->isPast());
    }
}
