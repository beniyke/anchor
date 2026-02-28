<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Academy\Events\BadgeAwardedEvent;
use Academy\Notifications\InApp\BadgeAwardedInAppNotification;
use Academy\Notifications\Mail\BadgeAwardedEmailNotification;
use Helpers\Data\Data;
use Notify\Notify;

class SendBadgeAwardedListener
{
    public function handle(BadgeAwardedEvent $event): void
    {
        $award = $event->award;
        $user = $award->user;

        $payload = Data::make([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'badge_name' => $award->badge->name,
            'url' => config('academy.urls.achievements', 'profile/achievements'),
        ]);

        Notify::email(BadgeAwardedEmailNotification::class, $payload);
        Notify::inapp(BadgeAwardedInAppNotification::class, $payload);
    }
}
