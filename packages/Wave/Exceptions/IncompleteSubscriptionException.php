<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when a subscription requires further action (e.g., payment).
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Exceptions;

class IncompleteSubscriptionException extends WaveException
{
    public function __construct(protected $subscription)
    {
        parent::__construct("The subscription requires further action to complete (payment required).");
    }

    public function getSubscription()
    {
        return $this->subscription;
    }
}
