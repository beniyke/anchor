<?php

declare(strict_types=1);

namespace App\Listeners;

use Activity\Activity;
use Core\Contracts\AuthServiceInterface;
use Core\Events\KernelTerminateEvent;
use Helpers\String\Str;

class LogActivityListener
{
    private AuthServiceInterface $auth;

    public function __construct(AuthServiceInterface $auth)
    {
        $this->auth = $auth;
    }

    public function handle(KernelTerminateEvent $event): void
    {
        $request = $event->request;

        if (! $request->isStateChanging()) {
            return;
        }

        $domain = $request->getRouteContext('domain');
        $entity = $request->getRouteContext('entity');
        $action = $request->getRouteContext('action');

        $userId = null;

        if ($this->auth->isAuthenticated()) {
            $userId = $this->auth->user()->id;
        }

        if (!class_exists(Activity::class)) {
            return;
        }

        $keywords = ['password', 'token', 'csrf', 'secret', 'key', '_token'];
        $meta = Str::maskSensitiveData($request->all(), $keywords);

        $description = sprintf(
            '%s action on %s%s',
            strtoupper($action ?? $request->method()),
            $entity ?? 'resource',
            $domain ? " in {$domain}" : ''
        );

        Activity::description($description)
            ->user($userId)
            ->metadata(array_merge($meta, [
                'method' => $request->method(),
                'url' => $request->uri(),
                'ip' => $request->ip(),
                'agent' => $request->userAgent()
            ]))
            ->tag('audit')
            ->level('info')
            ->immediate()
            ->log();
    }
}
