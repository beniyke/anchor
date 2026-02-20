<?php

declare(strict_types=1);

namespace App\Auth\Notifications\InApp;

use App\Core\BaseInAppNotification;

class LoginInAppNotification extends BaseInAppNotification
{
    public function getUser(): int
    {
        return $this->payload->get('id');
    }

    public function getMessage(): string
    {
        return 'There was a login to your account from a ' . $this->payload->get('browser') . ' Browser via ' . $this->payload->get('guard') . ' at ' . $this->payload->get('period');
    }

    public function getLabel(): string
    {
        return 'account';
    }

    public function getUrl(): ?string
    {
        return null;
    }
}
