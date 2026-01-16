<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fixed exchange rate provider
 * Stores exchange rates in memory
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Providers;

use Money\Contracts\ExchangeRateProvider;
use Money\Currency;
use Money\Exceptions\ExchangeRateNotFoundException;

/**
 * Fixed exchange rate provider
 *
 * Stores exchange rates in memory
 */
class FixedExchangeRateProvider implements ExchangeRateProvider
{
    private array $rates = [];

    /**
     * Set exchange rate
     */
    public function setRate(Currency|string $from, Currency|string $to, float $rate): self
    {
        $fromCode = is_string($from) ? $from : $from->getCode();
        $toCode = is_string($to) ? $to : $to->getCode();

        $this->rates[$fromCode][$toCode] = $rate;

        return $this;
    }

    public function getRate(Currency|string $from, Currency|string $to): float
    {
        $fromCode = is_string($from) ? $from : $from->getCode();
        $toCode = is_string($to) ? $to : $to->getCode();

        if ($fromCode === $toCode) {
            return 1.0;
        }

        if (isset($this->rates[$fromCode][$toCode])) {
            return $this->rates[$fromCode][$toCode];
        }

        if (isset($this->rates[$toCode][$fromCode])) {
            return 1.0 / $this->rates[$toCode][$fromCode];
        }

        throw ExchangeRateNotFoundException::forCurrencies($fromCode, $toCode);
    }

    public function hasRate(Currency|string $from, Currency|string $to): bool
    {
        $fromCode = is_string($from) ? $from : $from->getCode();
        $toCode = is_string($to) ? $to : $to->getCode();

        if ($fromCode === $toCode) {
            return true;
        }

        return isset($this->rates[$fromCode][$toCode]) ||
            isset($this->rates[$toCode][$fromCode]);
    }
}
