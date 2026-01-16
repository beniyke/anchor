<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Not Found Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use Exception;

class TransactionNotFoundException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("Transaction not found: {$identifier}", 404);
    }
}
