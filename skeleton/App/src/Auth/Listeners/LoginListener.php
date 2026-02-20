<?php

declare(strict_types=1);

namespace App\Auth\Listeners;

use App\Auth\Notifications\InApp\LoginInAppNotification;
use Helpers\Data\Data;
use Helpers\DateTimeHelper;
use Helpers\Http\UserAgent;
use Notify\Notify;
use Security\Auth\Events\LoginEvent;
use Security\Firewall\Drivers\AuthFirewall;

class LoginListener
{
    public function __construct(
        private readonly UserAgent $agent,
        private readonly AuthFirewall $firewall
    ) {
    }

    public function handle(LoginEvent $event): void
    {
        $user = $event->user;
        $data = [
            'browser' => $this->agent->browser(),
            'period' => DateTimeHelper::now()->format('D, M d Y h:i A'),
            'guard' => $event->guard,
            'id' => $user->getAuthId()
        ];

        $this->firewall->clear()->capture();
        $payload = Data::make($data);

        Notify::inapp(LoginInAppNotification::class, $payload);

        activity('logged in from a {browser} Browser at {period} via {guard}', $data, $user->getAuthId());
    }
}
