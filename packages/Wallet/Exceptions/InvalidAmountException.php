<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Invalid Amount Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use RuntimeException;

class InvalidAmountException extends RuntimeException
{
    public function __construct(string $message = 'Amount must be positive')
    {
        parent::__construct($message);
    }
}
