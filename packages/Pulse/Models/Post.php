<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Post.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $pulse_thread_id
 * @property int             $user_id
 * @property ?int            $parent_id
 * @property string          $content
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Thread $thread
 * @property-read User $author
 * @property-read ?Post $parent
 * @property-read ModelCollection $replies
 * @property-read ModelCollection $reactions
 */
class Post extends BaseModel
{
    protected string $table = 'pulse_post';

    protected array $fillable = [
        'pulse_thread_id',
        'user_id',
        'parent_id',
        'content',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'pulse_thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'pulse_post_id');
    }
}
