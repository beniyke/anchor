<?php

declare(strict_types=1);

namespace App\Auth\Listeners;

use Helpers\DateTimeHelper;
use Helpers\Http\UserAgent;
use Security\Auth\Events\LogoutEvent;

class LogoutListener
{
    public function __construct(
        private readonly UserAgent $agent
    ) {
    }

    public function handle(LogoutEvent $event): void
    {
        $user = $event->user;
        $data = [
            'browser' => $this->agent->browser(),
            'period' => DateTimeHelper::now()->format('D, M d Y h:i A'),
            'guard' => $event->guard,
        ];

        activity('logged out from {browser} via {guard} at {period}', $data, $user->getAuthId());
    }
}
