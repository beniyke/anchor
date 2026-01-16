<?php

declare(strict_types=1);

use Shield\Helpers\ShieldHelper;

if (! function_exists('shield')) {
    function shield(?string $driver = null): ShieldHelper
    {
        $manager = resolve(Shield\Services\ShieldManagerService::class);

        return new ShieldHelper($manager, $driver);
    }
}
