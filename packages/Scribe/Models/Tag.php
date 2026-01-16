<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Tag.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsToMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $name
 * @property string          $slug
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $posts
 */
class Tag extends BaseModel
{
    protected string $table = 'scribe_tag';

    protected array $fillable = [
        'refid',
        'name',
        'slug',
    ];

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'scribe_post_tag', 'scribe_tag_id', 'scribe_post_id');
    }
}
