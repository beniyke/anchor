<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown for invalid link tokens.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Exceptions;

use Exception;

class InvalidLinkException extends Exception
{
    public function __construct(string $message = 'The provided link token is invalid.')
    {
        parent::__construct($message);
    }
}
