<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Listener to send notification when a message is posted.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Listeners;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\Data\Data;
use Helpers\String\Str;
use Hub\Events\MessagePostedEvent;
use Hub\Notifications\MessagePostedNotification;
use Mail\Mail;

class SendMessagePostedNotificationListener
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function handle(MessagePostedEvent $event): void
    {
        $message = $event->message;
        $thread = $message->thread;
        $sender = $this->getUserById($event->userId);

        // Get all members except the sender who have notifications enabled
        $members = $thread->members()
            ->where('user_id', '!=', $event->userId)
            ->where('notifications_enabled', true)
            ->get();

        if ($members->isEmpty()) {
            return;
        }

        // Build recipients array
        $recipients = [];
        foreach ($members as $member) {
            $user = $this->getUserById($member->user_id);
            if ($user !== null && $user->email) {
                $recipients[$user->email] = $user->name ?? '';
            }
        }

        if (empty($recipients)) {
            return;
        }

        Mail::send(new MessagePostedNotification(Data::make([
            'recipients' => $recipients,
            'sender_name' => $sender?->name ?? 'Someone',
            'thread_title' => $thread->title ?? 'a conversation',
            'message_preview' => Str::limit($message->body, 100),
            'thread_url' => $this->getThreadUrl($thread->refid),
        ])));
    }

    private function getUserById(int $userId): ?User
    {
        return User::find($userId);
    }

    private function getThreadUrl(string $refid): string
    {
        $pattern = $this->config->get('hub.urls.thread', '/hub/thread/{refid}');

        return url(str_replace('{refid}', $refid, $pattern));
    }
}
