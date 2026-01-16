<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Thread model for conversations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\HasMany;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;
use Hub\Services\Builders\MemberBuilder;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $threadable_type
 * @property int             $threadable_id
 * @property string          $title
 * @property bool            $is_pinned
 * @property ?array          $metadata
 * @property int             $created_by
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read BaseModel $threadable
 * @property-read ModelCollection $members
 * @property-read ModelCollection $messages
 *
 * @method static Builder forResource(string $type, int $id)
 * @method static Builder forUser(int $userId)
 * @method static Builder pinned()
 */
class Thread extends BaseModel
{
    protected string $table = 'hub_thread';

    protected array $fillable = [
        'refid',
        'threadable_type',
        'threadable_id',
        'title',
        'is_pinned',
        'metadata',
        'created_by',
    ];

    protected array $casts = [
        'is_pinned' => 'bool',
        'metadata' => 'array',
        'created_by' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function threadable(): MorphTo
    {
        return $this->morphTo('threadable');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ThreadMember::class, 'thread_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    public function hasMember(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function getMember(int $userId): ?ThreadMember
    {
        return $this->members()->where('user_id', $userId)->first();
    }

    public function addMember(int $userId): MemberBuilder
    {
        return new MemberBuilder($this, $userId);
    }

    public function removeMember(int $userId): void
    {
        $member = $this->getMember($userId);
        if ($member !== null && !$member->isOwner()) {
            $member->delete();
        }
    }

    public function getMessageCount(): int
    {
        return $this->messages()->count();
    }

    public function getUnreadCount(int $userId): int
    {
        $member = $this->getMember($userId);
        if ($member === null || $member->last_read_at === null) {
            return $this->getMessageCount();
        }

        return $this->messages()
            ->where('created_at', '>', $member->last_read_at)
            ->count();
    }

    public function pin(): void
    {
        $this->update(['is_pinned' => true]);
    }

    public function unpin(): void
    {
        $this->update(['is_pinned' => false]);
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    public function scopeForResource(Builder $query, string $type, int $id): Builder
    {
        return $query->where('threadable_type', $type)
            ->where('threadable_id', $id);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereIn('id', function ($query) use ($userId) {
            $query->select('thread_id')
                ->from('hub_member')
                ->where('user_id', $userId);
        });
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }
}
