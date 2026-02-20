<?php

declare(strict_types=1);

namespace App\Auth\Listeners;

use Helpers\DateTimeHelper;
use Helpers\Http\UserAgent;
use Security\Auth\Events\LoginFailedEvent;
use Security\Firewall\Drivers\AuthFirewall;

class LoginFailedListener
{
    public function __construct(
        private readonly UserAgent $agent,
        private readonly AuthFirewall $firewall
    ) {
    }

    public function handle(LoginFailedEvent $event): void
    {
        $data = [
            'browser' => $this->agent->browser(),
            'period' => DateTimeHelper::now()->format('D, M d Y h:i A'),
            'guard' => $event->guard,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ];

        $this->firewall->fail()->capture();

        activity('failed login attempt from {ip} using {browser} via {guard} at {period}', $data);
    }
}
