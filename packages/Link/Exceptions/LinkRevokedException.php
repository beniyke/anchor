<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when accessing a revoked link.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Exceptions;

use Exception;

class LinkRevokedException extends Exception
{
    public function __construct(string $message = 'This link has been revoked.')
    {
        parent::__construct($message);
    }
}
