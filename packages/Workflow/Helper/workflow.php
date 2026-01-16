<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Helper functions for workflow durability and side effects.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Workflow\Commands\SideEffect;
use Workflow\Commands\Timer;

if (! function_exists('days')) {
    function days(int $days): Timer
    {
        return new Timer($days * 24 * 60 * 60);
    }
}

if (! function_exists('minutes')) {
    function minutes(int $minutes): Timer
    {
        return new Timer($minutes * 60);
    }
}

if (! function_exists('sideEffect')) {
    function sideEffect(callable $callback): SideEffect
    {
        return new SideEffect($callback instanceof Closure ? $callback : Closure::fromCallable($callback));
    }
}
