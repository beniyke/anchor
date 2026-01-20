<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Helper functions for the package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Shield\Helpers\ShieldHelper;

if (! function_exists('shield')) {
    function shield(?string $driver = null): ShieldHelper
    {
        $manager = resolve(Shield\Services\ShieldManagerService::class);

        return new ShieldHelper($manager, $driver);
    }
}
