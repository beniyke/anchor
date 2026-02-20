<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Listener to send notification when a reminder is due.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Listeners;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\Data\Data;
use Hub\Events\ReminderDueEvent;
use Hub\Notifications\ReminderNotification;
use Mail\Mail;

class SendReminderNotificationListener
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function handle(ReminderDueEvent $event): void
    {
        $user = $this->getUserById($event->userId);
        if ($user === null) {
            return;
        }

        $reminder = $event->reminder;
        $remindable = $reminder->remindable;

        Mail::send(new ReminderNotification(Data::make([
            'email' => $user->email,
            'name' => $user->name ?? 'there',
            'message' => $reminder->message,
            'resource_title' => $remindable?->title ?? $remindable?->name ?? null,
            'action_url' => $this->getReminderUrl($reminder->refid),
        ])));
    }

    private function getUserById(int $userId): ?User
    {
        return User::find($userId);
    }

    private function getReminderUrl(string $refid): string
    {
        $pattern = $this->config->get('hub.urls.reminder', '/hub/reminder/{refid}');

        return url(str_replace('{refid}', $refid, $pattern));
    }
}
