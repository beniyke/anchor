<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Env Updater
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Services;

use Core\Services\DotenvInterface;
use Exception;

class EnvUpdaterService
{
    public static function update(string $key, string $value): bool
    {
        try {
            /** @var DotenvInterface $dotenv */
            $dotenv = resolve(DotenvInterface::class);
            $dotenv->setValue($key, $value);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
