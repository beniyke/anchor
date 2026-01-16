<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification class for sending OTP via SMS.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Channels\Notifications;

use Notify\Notifications\MessageNotification;

class SendOtpSmsNotification extends MessageNotification
{
    public function getRecipient(): string
    {
        return $this->payload->get('phone');
    }

    public function getMessage(): string
    {
        return "Your verification code is {$this->payload->get('code')}. It expires in 15 minutes.";
    }
}
