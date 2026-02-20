<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Listener to send notification when a user is mentioned.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Listeners;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\Data\Data;
use Helpers\String\Str;
use Hub\Events\UserMentionedEvent;
use Hub\Notifications\MentionNotification;
use Mail\Mail;

class SendMentionNotificationListener
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function handle(UserMentionedEvent $event): void
    {
        $user = $this->getUserById($event->mentionedUserId);
        if ($user === null) {
            return;
        }

        $message = $event->message;
        $thread = $message->thread;
        $sender = $this->getUserById($message->user_id);

        Mail::send(new MentionNotification(Data::make([
            'email' => $user->email,
            'name' => $user->name ?? 'there',
            'mentioner_name' => $sender?->name ?? 'Someone',
            'thread_title' => $thread?->title ?? 'a conversation',
            'message_preview' => Str::limit($message->body, 100),
            'thread_url' => $this->getThreadUrl($thread?->refid),
        ])));
    }

    private function getUserById(int $userId): ?User
    {
        return User::find($userId);
    }

    private function getThreadUrl(?string $refid): string
    {
        if ($refid === null) {
            return '#';
        }

        $pattern = $this->config->get('hub.urls.thread', '/hub/thread/{refid}');

        return url(str_replace('{refid}', $refid, $pattern));
    }
}
