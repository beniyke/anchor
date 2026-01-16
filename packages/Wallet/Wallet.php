<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Provides convenient static access to wallet operations.
 * It mostly delegates calls to the underlying WalletManagerService.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet;

use Wallet\Services\WalletManagerService;

class Wallet
{
    public static function __callStatic(string $method, array $arguments)
    {
        return resolve(WalletManagerService::class)->$method(...$arguments);
    }
}
