<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Insufficient Funds Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct(int $walletId, int $required, int $available)
    {
        $message = sprintf(
            'Insufficient funds in wallet %d. Required: %d, Available: %d',
            $walletId,
            $required,
            $available
        );

        parent::__construct($message, 400);
    }
}
