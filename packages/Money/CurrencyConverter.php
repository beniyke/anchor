<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Currency converter.
 * Converts money between currencies using exchange rates.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

class CurrencyConverter
{
    public function __construct(
        private ExchangeRateProvider $rateProvider
    ) {
    }

    /**
     * Convert money to another currency
     */
    public function convert(Money $money, Currency|string $toCurrency): Money
    {
        $toCurrency = is_string($toCurrency) ? Currency::of($toCurrency) : $toCurrency;

        if ($money->getCurrency()->equals($toCurrency)) {
            return $money;
        }

        $rate = $this->rateProvider->getRate($money->getCurrency(), $toCurrency);

        $convertedAmount = bcmul((string) $money->getAmount(), (string) $rate, 0);

        return Money::make($convertedAmount, $toCurrency);
    }

    public function getRateProvider(): ExchangeRateProvider
    {
        return $this->rateProvider;
    }
}
