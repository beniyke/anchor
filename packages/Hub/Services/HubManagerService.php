<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core hub manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Services;

use Core\Event;
use Core\Services\ConfigServiceInterface;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Hub\Enums\ReminderStatus;
use Hub\Enums\ThreadRole;
use Hub\Events\MessagePostedEvent;
use Hub\Events\ReminderDueEvent;
use Hub\Events\ThreadCreatedEvent;
use Hub\Events\UserMentionedEvent;
use Hub\Models\Mention;
use Hub\Models\Message;
use Hub\Models\Reaction;
use Hub\Models\Reminder;
use Hub\Models\Thread;
use Hub\Models\ThreadMember;

class HubManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function createThread(array $data): Thread
    {
        $thread = Thread::create([
            'refid' => Str::random('secure'),
            'threadable_type' => $data['threadable_type'] ?? null,
            'threadable_id' => $data['threadable_id'] ?? null,
            'title' => $data['title'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? false,
            'metadata' => $data['metadata'] ?? [],
            'created_by' => $data['created_by'] ?? null,
        ]);

        // Add creator as owner
        if (isset($data['created_by'])) {
            $this->addMember($thread, $data['created_by'], ThreadRole::OWNER);
        }

        // Add additional members
        if (!empty($data['members'])) {
            foreach ($data['members'] as $userId) {
                if ($userId !== ($data['created_by'] ?? null)) {
                    $this->addMember($thread, $userId, ThreadRole::MEMBER);
                }
            }
        }

        Event::dispatch(new ThreadCreatedEvent($thread, $data['created_by'] ?? 0));

        return $thread;
    }

    public function addMember(Thread $thread, int $userId, ThreadRole $role = ThreadRole::MEMBER): ThreadMember
    {
        $existing = $thread->getMember($userId);
        if ($existing !== null) {
            return $existing;
        }

        return ThreadMember::create([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'role' => $role,
            'notifications_enabled' => $this->config->get('hub.default_notifications_enabled', true),
            'joined_at' => DateTimeHelper::now(),
        ]);
    }

    public function removeMember(Thread $thread, int $userId): void
    {
        $member = $thread->getMember($userId);
        if ($member !== null && !$member->isOwner()) {
            $member->delete();
        }
    }

    /**
     * Post a message to a thread.
     */
    public function postMessage(array $data): Message
    {
        $message = Message::create([
            'refid' => Str::random('secure'),
            'thread_id' => $data['thread_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'user_id' => $data['user_id'],
            'body' => $data['body'],
            'is_pinned' => $data['is_pinned'] ?? false,
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Extract and create mentions
        $mentionedUserIds = $data['mentions'] ?? $this->parseMentions($data['body']);
        foreach ($mentionedUserIds as $mentionedUserId) {
            $mention = Mention::create([
                'message_id' => $message->id,
                'user_id' => $mentionedUserId,
            ]);

            Event::dispatch(new UserMentionedEvent($mention, $message, $mentionedUserId));
        }

        Event::dispatch(new MessagePostedEvent($message, $data['thread_id'], $data['user_id']));

        return $message;
    }

    /**
     * Parse @mentions from message body.
     */
    public function parseMentions(string $body): array
    {
        $pattern = $this->config->get('hub.mention_pattern', '/@(\w+)/');
        preg_match_all($pattern, $body, $matches);

        return $matches[1] ?? [];
    }

    public function addReaction(Message $message, string $emoji, int $userId): ?Reaction
    {
        if ($message->hasReaction($userId, $emoji)) {
            return null;
        }

        return Reaction::create([
            'message_id' => $message->id,
            'user_id' => $userId,
            'emoji' => $emoji,
            'created_at' => DateTimeHelper::now(),
        ]);
    }

    public function removeReaction(Message $message, string $emoji, int $userId): void
    {
        Reaction::onMessage($message->id)
            ->byUser($userId)
            ->withEmoji($emoji)
            ->delete();
    }

    public function toggleReaction(Message $message, string $emoji, int $userId): bool
    {
        if ($message->hasReaction($userId, $emoji)) {
            $this->removeReaction($message, $emoji, $userId);

            return false;
        }

        $this->addReaction($message, $emoji, $userId);

        return true;
    }

    public function createReminder(array $data): Reminder
    {
        return Reminder::create([
            'refid' => Str::random('secure'),
            'user_id' => $data['user_id'],
            'remindable_type' => $data['remindable_type'] ?? null,
            'remindable_id' => $data['remindable_id'] ?? null,
            'message' => $data['message'],
            'remind_at' => $data['remind_at'],
            'repeat_interval' => $data['repeat_interval'] ?? 'none',
            'status' => ReminderStatus::PENDING,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * Mark thread as read for a user.
     */
    public function markAsRead(Thread $thread, int $userId): void
    {
        $member = $thread->getMember($userId);
        if ($member !== null) {
            $member->markAsRead();
        }
    }

    public function processDueReminders(): int
    {
        $reminders = Reminder::due()->get();
        $count = 0;

        foreach ($reminders as $reminder) {
            Event::dispatch(new ReminderDueEvent($reminder, $reminder->user_id));

            if ($reminder->repeats()) {
                $reminder->rescheduleNext();
            } else {
                $reminder->complete();
            }

            $count++;
        }

        return $count;
    }

    public function getThreadsForUser(int $userId): array
    {
        return Thread::forUser($userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->all();
    }

    public function getMessages(Thread $thread, int $limit = 50, int $offset = 0): array
    {
        return $thread->messages()
            ->root()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->all();
    }

    public function getUpcomingReminders(int $userId, int $days = 7): array
    {
        return Reminder::forUser($userId)
            ->upcoming($days)
            ->get()
            ->all();
    }
}
