<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when the owner's wallet has insufficient funds.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Exceptions;

class InsufficientFundsException extends BillingException
{
    public function __construct()
    {
        parent::__construct("The wallet has insufficient funds to process the subscription.");
    }
}
