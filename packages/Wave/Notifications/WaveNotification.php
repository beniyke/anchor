<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Base notification class for Wave.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Notifications;

use Mail\EmailNotification;

abstract class WaveNotification extends EmailNotification
{
    /**
     * Define who receives the email.
     */
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name') ?? '',
            ],
        ];
    }

    /**
     * Define the inner title (header) of the email.
     */
    public function getTitle(): string
    {
        return $this->getSubject();
    }
}
