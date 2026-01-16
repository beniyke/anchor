<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Currency data repository.
 * Stores and retrieves currency information.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

class CurrencyRepository
{
    private static ?array $currencies = null;

    public static function find(string $code): ?array
    {
        self::loadCurrencies();

        return self::$currencies[strtoupper($code)] ?? null;
    }

    public static function all(): array
    {
        self::loadCurrencies();

        return self::$currencies;
    }

    public static function exists(string $code): bool
    {
        return self::find($code) !== null;
    }

    /**
     * Load currency data
     */
    private static function loadCurrencies(): void
    {
        if (self::$currencies !== null) {
            return;
        }

        self::$currencies = require __DIR__ . '/Data/currencies.php';
    }
}
