<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Thread.
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
 * @property int             $pulse_channel_id
 * @property int             $user_id
 * @property string          $title
 * @property string          $slug
 * @property bool            $is_pinned
 * @property bool            $is_locked
 * @property int             $view_count
 * @property ?DateTimeHelper $last_activity_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Channel $channel
 * @property-read User $author
 * @property-read ModelCollection $posts
 * @property-read ModelCollection $subscriptions
 */
class Thread extends BaseModel
{
    protected string $table = 'pulse_thread';

    protected array $fillable = [
        'pulse_channel_id',
        'user_id',
        'title',
        'slug',
        'is_pinned',
        'is_locked',
        'view_count',
        'last_activity_at',
    ];

    protected array $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'pulse_channel_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'pulse_thread_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'pulse_thread_id');
    }
}
