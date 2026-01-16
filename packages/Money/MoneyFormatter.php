<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Money formatter.
 * Formats money values for display.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

use NumberFormatter;

class MoneyFormatter
{
    public function format(Money $money, ?string $locale = null): string
    {
        if ($locale && extension_loaded('intl')) {
            return $this->formatIntl($money, $locale);
        }

        return $this->formatSimple($money);
    }

    public function formatSimple(Money $money): string
    {
        $currency = $money->getCurrency();
        $symbol = $currency->getSymbol();
        $amount = $money->getMajorAmount();
        $decimals = $currency->getMinorUnit();

        return $symbol . number_format($amount, $decimals, '.', ',');
    }

    public function formatIntl(Money $money, string $locale = 'en_US'): string
    {
        if (! extension_loaded('intl')) {
            return $this->formatSimple($money);
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency(
            $money->getMajorAmount(),
            $money->getCurrency()->getCode()
        );
    }

    public function formatByDecimal(Money $money, int $decimals = 2): string
    {
        $symbol = $money->getCurrency()->getSymbol();
        $amount = $money->getMajorAmount();

        return $symbol . number_format($amount, $decimals, '.', ',');
    }

    public function formatWithoutSymbol(Money $money): string
    {
        $amount = $money->getMajorAmount();
        $decimals = $money->getCurrency()->getMinorUnit();

        return number_format($amount, $decimals, '.', ',');
    }
}
