<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for hub operations.
 *
 * @method static Thread              createThread(array $data)                    Create a thread
 * @method static Message             postMessage(array $data)                     Post a message
 * @method static Reaction|null       addReaction($message, $emoji, $userId)       Add reaction
 * @method static void                removeReaction($message, $emoji, $userId)    Remove reaction
 * @method static bool                toggleReaction($message, $emoji, $userId)    Toggle reaction
 * @method static Reminder            createReminder(array $data)                  Create a reminder
 * @method static void                markAsRead(Thread $thread, int $userId)      Mark thread as read
 * @method static int                 processDueReminders()                        Process due reminders
 * @method static array               getThreadsForUser(int $userId)               Get user's threads
 * @method static array               getMessages(Thread $thread)                  Get thread messages
 * @method static array               getUpcomingReminders(int $userId, int $days) Get upcoming reminders
 * @method static HubAnalyticsService analytics()                                  Get analytics service
 *
 * @see HubManagerService
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub;

use Hub\Models\Message;
use Hub\Models\Reaction;
use Hub\Models\Reminder;
use Hub\Models\Thread;
use Hub\Services\Builders\MessageBuilder;
use Hub\Services\Builders\ReminderBuilder;
use Hub\Services\Builders\ThreadBuilder;
use Hub\Services\HubAnalyticsService;
use Hub\Services\HubManagerService;
use Link\Link;

class Hub
{
    /**
     * Create a new thread builder.
     */
    public static function thread(): ThreadBuilder
    {
        return new ThreadBuilder(resolve(HubManagerService::class));
    }

    /**
     * Create a new message builder.
     */
    public static function message(): MessageBuilder
    {
        return new MessageBuilder(resolve(HubManagerService::class));
    }

    /**
     * Create a new reminder builder.
     */
    public static function reminder(): ReminderBuilder
    {
        return new ReminderBuilder(resolve(HubManagerService::class));
    }

    /**
     * Add a reaction to a message.
     */
    public static function react(Message $message, string $emoji, int $userId): void
    {
        resolve(HubManagerService::class)->addReaction($message, $emoji, $userId);
    }

    /**
     * Create an invite link for a thread (via Link package).
     */
    public static function invite(Thread $thread, ?string $recipientEmail = null): mixed
    {
        $builder = Link::make()
            ->for($thread)
            ->invite();

        if ($recipientEmail !== null) {
            $builder->recipient($recipientEmail);
        }

        return $builder->create();
    }

    public static function find(string $refid): ?Thread
    {
        return Thread::findByRefid($refid);
    }

    public static function findMessage(string $refid): ?Message
    {
        return Message::findByRefid($refid);
    }

    public static function findReminder(string $refid): ?Reminder
    {
        return Reminder::findByRefid($refid);
    }

    public static function analytics(): HubAnalyticsService
    {
        return resolve(HubAnalyticsService::class);
    }

    /**
     * Forward static calls to HubManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(HubManagerService::class)->$method(...$arguments);
    }
}
