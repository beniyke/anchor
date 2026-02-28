<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

use Notify\Notifications\DatabaseNotification;

abstract class AcademyInAppNotification extends DatabaseNotification
{
    public function getUser(): int
    {
        return $this->payload->get('user_id');
    }

    public function getLabel(): string
    {
        return 'Academy';
    }

    public function getUrl(): ?string
    {
        return url($this->payload->get('url'));
    }
}
