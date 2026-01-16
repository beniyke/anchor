<?php

declare(strict_types=1);

/**
 * Base class for handling InApp notifications.
 * This class provides a structure for building InApp notifications
 * and requires extending classes to implement key notification details.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Core;

use Notify\Notifications\DatabaseNotification;

abstract class BaseInAppNotification extends DatabaseNotification
{
}
