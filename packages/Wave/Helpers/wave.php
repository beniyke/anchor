<?php

declare(strict_types=1);

use Wave\Services\WaveManagerService;

/**
 * Wave Package Helper
 *
 * Provides global access to the WaveManagerService instance.
 */
if (! function_exists('wave')) {
    function wave(): WaveManagerService
    {
        return resolve(WaveManagerService::class);
    }
}
