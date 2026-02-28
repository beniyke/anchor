<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Activity\Activity;
use Helpers\String\Str;

class LogActivityListener
{
    public function handle(object $event): void
    {
        if (!class_exists(Activity::class)) {
            return;
        }

        $parts = explode('\\', get_class($event));
        $name = end($parts);
        $snakeName = Str::snake($name);

        Activity::description("academy." . $snakeName)
            ->subject($this->getSubject($event))
            ->user($this->getCauser($event)?->id)
            ->level('info')
            ->log();
    }

    protected function getSubject(object $event): ?object
    {
        return $event->enrolment ?? $event->submission ?? $event->session ?? null;
    }

    protected function getCauser(object $event): ?object
    {
        return $event->user ?? ($event->enrolment->user ?? null);
    }
}
