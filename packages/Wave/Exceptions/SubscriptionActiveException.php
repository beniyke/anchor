<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when attempting to subscribe a user who already has an active subscription.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Exceptions;

class SubscriptionActiveException extends WaveException
{
    public function __construct()
    {
        parent::__construct("The customer already has an active subscription.");
    }
}
