<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when link usage limit exceeded.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Exceptions;

use Exception;

class LinkUsageExceededException extends Exception
{
    public function __construct(string $message = 'This link has reached its maximum usage limit.')
    {
        parent::__construct($message);
    }
}
