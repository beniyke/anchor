<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Thread member model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;
use Hub\Enums\ThreadRole;

/**
 * @property int             $id
 * @property int             $thread_id
 * @property int             $user_id
 * @property ThreadRole      $role
 * @property ?DateTimeHelper $last_read_at
 * @property bool            $notifications_enabled
 * @property ?DateTimeHelper $joined_at
 * @property-read Thread $thread
 */
class ThreadMember extends BaseModel
{
    public const TABLE = 'hub_member';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'thread_id',
        'user_id',
        'role',
        'last_read_at',
        'notifications_enabled',
        'joined_at',
    ];

    protected array $casts = [
        'thread_id' => 'int',
        'user_id' => 'int',
        'role' => ThreadRole::class,
        'last_read_at' => 'datetime',
        'notifications_enabled' => 'bool',
        'joined_at' => 'datetime',
    ];

    public bool $timestamps = false;

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function isOwner(): bool
    {
        return $this->role === ThreadRole::OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->role === ThreadRole::ADMIN;
    }

    public function canManage(): bool
    {
        return $this->role?->canManageMembers() ?? false;
    }

    public function markAsRead(): void
    {
        $this->update(['last_read_at' => DateTimeHelper::now()]);
    }

    public function toggleNotifications(): void
    {
        $this->update(['notifications_enabled' => !$this->notifications_enabled]);
    }

    public function setRole(ThreadRole $role): void
    {
        $this->update(['role' => $role]);
    }
}
