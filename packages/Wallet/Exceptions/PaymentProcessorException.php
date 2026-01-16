<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Payment Processor Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use Exception;

class PaymentProcessorException extends Exception
{
    public function __construct(string $processor, string $message, int $code = 500)
    {
        parent::__construct("[{$processor}] {$message}", $code);
    }
}
