<?php

declare(strict_types=1);

/**
 * Base class for handling Message notifications.
 * This class provides a structure for building Messaging (SMS, WhatsApp etc) notifications
 * and requires extending classes to implement key notification details.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Core;

use Notify\Notifications\MessageNotification;

abstract class BaseMessageNotification extends MessageNotification
{
}
