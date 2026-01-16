<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Insufficient Credits Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Exceptions;

class InsufficientCreditsException extends AllyException
{
    public static function create(int $balance, int $required): self
    {
        return new self("Insufficient distribution credits. Balance: {$balance}, Required: {$required}.");
    }
}
