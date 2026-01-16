<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when accessing an expired link.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Exceptions;

use Exception;

class LinkExpiredException extends Exception
{
    public function __construct(string $message = 'This link has expired.')
    {
        parent::__construct($message);
    }
}
