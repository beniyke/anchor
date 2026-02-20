<?php

declare(strict_types=1);

use Money\Currency;
use Money\Money;

/**
 * Money Package helpers
 */

if (! function_exists('money')) {
    /**
     * Create a Money instance from major units (e.g., dollars).
     *
     * @param float|int|string $amount
     * @param Currency|string  $currency
     *
     * @return Money
     */
    function money(float|int|string $amount = 0, string|Currency $currency = 'USD'): Money
    {
        return Money::amount($amount, $currency);
    }
}

if (! function_exists('money_minor')) {
    /**
     * Create a Money instance from minor units (e.g., cents).
     *
     * @param int|string      $amount
     * @param Currency|string $currency
     *
     * @return Money
     */
    function money_minor(int|string $amount = 0, string|Currency $currency = 'USD'): Money
    {
        return Money::make($amount, $currency);
    }
}
