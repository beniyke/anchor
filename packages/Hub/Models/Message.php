<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Message model for thread conversations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $thread_id
 * @property ?int            $parent_id
 * @property int             $user_id
 * @property string          $body
 * @property bool            $is_pinned
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Thread $thread
 * @property-read ?Message $parent
 * @property-read ModelCollection $replies
 * @property-read ModelCollection $mentions
 * @property-read ModelCollection $reactions
 *
 * @method static Builder inThread(int $threadId)
 * @method static Builder pinned()
 * @method static Builder root()
 */
class Message extends BaseModel
{
    protected string $table = 'hub_message';

    protected array $fillable = [
        'refid',
        'thread_id',
        'parent_id',
        'user_id',
        'body',
        'is_pinned',
        'metadata',
    ];

    protected array $casts = [
        'thread_id' => 'int',
        'parent_id' => 'int',
        'user_id' => 'int',
        'is_pinned' => 'bool',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function mentions(): HasMany
    {
        return $this->hasMany(Mention::class, 'message_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'message_id');
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function pin(): void
    {
        $this->update(['is_pinned' => true]);
    }

    public function unpin(): void
    {
        $this->update(['is_pinned' => false]);
    }

    /**
     * Check if user reacted with emoji.
     */
    public function hasReaction(int $userId, string $emoji): bool
    {
        return $this->reactions()
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->exists();
    }

    public function getReactionCounts(): array
    {
        $reactions = $this->reactions()->get();
        $counts = [];

        foreach ($reactions as $reaction) {
            $emoji = $reaction->emoji;
            $counts[$emoji] = ($counts[$emoji] ?? 0) + 1;
        }

        return $counts;
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    public function scopeInThread(Builder $query, int $threadId): Builder
    {
        return $query->where('thread_id', $threadId);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
