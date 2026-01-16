<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exchange rate provider interface
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Contracts;

use Money\Currency;

/**
 * Exchange rate provider interface
 */
interface ExchangeRateProvider
{
    public function getRate(Currency|string $from, Currency|string $to): float;

    public function hasRate(Currency|string $from, Currency|string $to): bool;
}
