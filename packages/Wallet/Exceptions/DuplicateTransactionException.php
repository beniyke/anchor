<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Duplicate Transaction Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use Exception;

class DuplicateTransactionException extends Exception
{
    public function __construct(string $referenceId)
    {
        parent::__construct("Duplicate transaction detected: {$referenceId}", 409);
    }
}
