<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when a subscription plan is not found.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Exceptions;

class PlanNotFoundException extends WaveException
{
    public static function forId(string|int $id): self
    {
        return new self("Plan with ID '{$id}' not found.");
    }
}
