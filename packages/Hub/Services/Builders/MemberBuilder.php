<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent member builder for thread member addition.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Services\Builders;

use Helpers\DateTimeHelper;
use Hub\Enums\ThreadRole;
use Hub\Models\Thread;
use Hub\Models\ThreadMember;

class MemberBuilder
{
    private ThreadRole $role = ThreadRole::MEMBER;

    private bool $notificationsEnabled = true;

    public function __construct(
        private readonly Thread $thread,
        private readonly int $userId
    ) {
    }

    /**
     * Set role as owner.
     */
    public function asOwner(): self
    {
        $this->role = ThreadRole::OWNER;

        return $this;
    }

    /**
     * Set role as admin.
     */
    public function asAdmin(): self
    {
        $this->role = ThreadRole::ADMIN;

        return $this;
    }

    /**
     * Set role as member.
     */
    public function asMember(): self
    {
        $this->role = ThreadRole::MEMBER;

        return $this;
    }

    /**
     * Set role as guest.
     */
    public function asGuest(): self
    {
        $this->role = ThreadRole::GUEST;

        return $this;
    }

    /**
     * Disable notifications.
     */
    public function silent(): self
    {
        $this->notificationsEnabled = false;

        return $this;
    }

    /**
     * Enable notifications (default).
     */
    public function withNotifications(): self
    {
        $this->notificationsEnabled = true;

        return $this;
    }

    /**
     * Add the member and return the ThreadMember.
     */
    public function add(): ThreadMember
    {
        $existing = $this->thread->getMember($this->userId);
        if ($existing !== null) {
            return $existing;
        }

        return ThreadMember::create([
            'thread_id' => $this->thread->id,
            'user_id' => $this->userId,
            'role' => $this->role,
            'notifications_enabled' => $this->notificationsEnabled,
            'joined_at' => DateTimeHelper::now(),
        ]);
    }
}
