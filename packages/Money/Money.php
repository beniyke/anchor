<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Immutable Money value object.
 * Represents a monetary amount with currency.
 * All amounts are stored as integers (smallest currency unit).
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

use InvalidArgumentException;
use JsonSerializable;
use Money\Exceptions\CurrencyMismatchException;
use Money\Exceptions\InvalidAmountException;

class Money implements JsonSerializable
{
    private function __construct(
        private readonly int|string $amount,
        private readonly Currency $currency
    ) {
    }

    /**
     * Create money from minor units (e.g., cents) - PRIMARY METHOD
     */
    public static function make(int|string $amount, string|Currency $currency): self
    {
        if (is_string($amount) && ! is_numeric($amount)) {
            throw InvalidAmountException::invalidAmount($amount);
        }

        $currency = is_string($currency) ? Currency::of($currency) : $currency;

        return new self($amount, $currency);
    }

    /**
     * Alias: create() - more fluent naming
     */
    public static function create(int|string $amount, string|Currency $currency): self
    {
        return self::make($amount, $currency);
    }

    /**
     * Alias: from() - more fluent naming
     */
    public static function from(int|string $amount, string|Currency $currency): self
    {
        return self::make($amount, $currency);
    }

    /**
     * Create from cents - intuitive naming
     */
    public static function cents(int|string $amount, string|Currency $currency): self
    {
        return self::make($amount, $currency);
    }

    /**
     * Create from major units (e.g., dollars)
     */
    public static function amount(float|int|string $amount, string|Currency $currency): self
    {
        $currency = is_string($currency) ? Currency::of($currency) : $currency;
        $subunit = $currency->getSubunit();

        // Use BCMath for precision
        $minorAmount = bcmul((string) $amount, (string) $subunit, 0);

        return new self($minorAmount, $currency);
    }

    /**
     * Create from dollars - intuitive naming
     */
    public static function dollars(float|int|string $amount, string|Currency $currency = 'USD'): self
    {
        return self::amount($amount, $currency);
    }

    public static function euros(float|int|string $amount): self
    {
        return self::amount($amount, 'EUR');
    }

    public static function pounds(float|int|string $amount): self
    {
        return self::amount($amount, 'GBP');
    }

    public static function yen(float|int|string $amount): self
    {
        return self::amount($amount, 'JPY');
    }

    public static function yuan(float|int|string $amount): self
    {
        return self::amount($amount, 'CNY');
    }

    public static function rupees(float|int|string $amount): self
    {
        return self::amount($amount, 'INR');
    }

    public static function reals(float|int|string $amount): self
    {
        return self::amount($amount, 'BRL');
    }

    public static function pesos(float|int|string $amount): self
    {
        return self::amount($amount, 'MXN');
    }

    public static function rand(float|int|string $amount): self
    {
        return self::amount($amount, 'ZAR');
    }

    public static function rubles(float|int|string $amount): self
    {
        return self::amount($amount, 'RUB');
    }

    public static function won(float|int|string $amount): self
    {
        return self::amount($amount, 'KRW');
    }

    public static function francs(float|int|string $amount): self
    {
        return self::amount($amount, 'CHF');
    }

    public static function krona(float|int|string $amount): self
    {
        return self::amount($amount, 'SEK');
    }

    public static function zero(string|Currency $currency): self
    {
        return self::make(0, $currency);
    }

    public static function fromArray(array $data): self
    {
        return self::make($data['amount'], $data['currency']);
    }

    /**
     * Convert to another currency
     */
    public function convertTo(Currency|string $toCurrency, CurrencyConverter $converter): self
    {
        return $converter->convert($this, $toCurrency);
    }

    public function add(Money ...$addends): self
    {
        $result = $this->amount;

        foreach ($addends as $addend) {
            $this->assertSameCurrency($addend);
            $result = bcadd((string) $result, (string) $addend->amount, 0);
        }

        return new self($result, $this->currency);
    }

    public function subtract(Money ...$subtrahends): self
    {
        $result = $this->amount;

        foreach ($subtrahends as $subtrahend) {
            $this->assertSameCurrency($subtrahend);
            $result = bcsub((string) $result, (string) $subtrahend->amount, 0);
        }

        return new self($result, $this->currency);
    }

    public function multiply(int|float|string $multiplier, int $roundingMode = RoundingMode::HALF_UP): self
    {
        $result = bcmul((string) $this->amount, (string) $multiplier, 10);
        $rounded = $this->round($result, $roundingMode);

        return new self($rounded, $this->currency);
    }

    /**
     * Multiply and round up (ceiling)
     */
    public function multiplyAndRoundUp(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::CEILING);
    }

    /**
     * Multiply and round down (floor)
     */
    public function multiplyAndRoundDown(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::FLOOR);
    }

    /**
     * Multiply and round half up (default)
     */
    public function multiplyAndRoundHalfUp(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::HALF_UP);
    }

    /**
     * Multiply and round half down
     */
    public function multiplyAndRoundHalfDown(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::HALF_DOWN);
    }

    /**
     * Multiply and round half even (banker's rounding)
     */
    public function multiplyAndRoundHalfEven(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::HALF_EVEN);
    }

    /**
     * Alias for multiplyAndRoundUp - rounds towards positive infinity
     */
    public function roundUp(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::UP);
    }

    /**
     * Alias for multiplyAndRoundDown - rounds towards zero
     */
    public function roundDown(int|float|string $multiplier): self
    {
        return $this->multiply($multiplier, RoundingMode::DOWN);
    }

    public function divide(int|float|string $divisor, int $roundingMode = RoundingMode::HALF_UP): self
    {
        if ((float) $divisor == 0) {
            throw InvalidAmountException::divisionByZero();
        }

        $result = bcdiv((string) $this->amount, (string) $divisor, 10);
        $rounded = $this->round($result, $roundingMode);

        return new self($rounded, $this->currency);
    }

    public function mod(Money $divisor): self
    {
        $this->assertSameCurrency($divisor);

        $result = bcmod((string) $this->amount, (string) $divisor->amount);

        return new self($result, $this->currency);
    }

    public function absolute(): self
    {
        if ($this->isNegative()) {
            return new self(bcmul((string) $this->amount, '-1', 0), $this->currency);
        }

        return $this;
    }

    /**
     * Negate the amount
     */
    public function negative(): self
    {
        return new self(bcmul((string) $this->amount, '-1', 0), $this->currency);
    }

    public function percentage(int|float $percentage): self
    {
        return $this->multiply($percentage / 100);
    }

    public function addPercentage(int|float $percentage): self
    {
        return $this->add($this->percentage($percentage));
    }

    public function subtractPercentage(int|float $percentage): self
    {
        return $this->subtract($this->percentage($percentage));
    }

    public function ratioOf(Money $other): float
    {
        $this->assertSameCurrency($other);

        if ($other->isZero()) {
            return 0.0;
        }

        return (float) bcdiv((string) $this->amount, (string) $other->amount, 10) * 100;
    }

    public function allocate(array $ratios): array
    {
        $allocator = new Allocator();

        return $allocator->allocate($this, $ratios);
    }

    /**
     * Allocate equally to N parts
     */
    public function allocateTo(int $n): array
    {
        $allocator = new Allocator();

        return $allocator->allocateTo($this, $n);
    }

    public function equals(Money $other): bool
    {
        return $this->isSameCurrency($other) &&
            bccomp((string) $this->amount, (string) $other->amount, 0) === 0;
    }

    public function greaterThan(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return bccomp((string) $this->amount, (string) $other->amount, 0) === 1;
    }

    public function greaterThanOrEqual(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return bccomp((string) $this->amount, (string) $other->amount, 0) >= 0;
    }

    public function lessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return bccomp((string) $this->amount, (string) $other->amount, 0) === -1;
    }

    public function lessThanOrEqual(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return bccomp((string) $this->amount, (string) $other->amount, 0) <= 0;
    }

    /**
     * Compare with another money (-1, 0, 1)
     */
    public function compare(Money $other): int
    {
        $this->assertSameCurrency($other);

        return bccomp((string) $this->amount, (string) $other->amount, 0);
    }

    public function isZero(): bool
    {
        return bccomp((string) $this->amount, '0', 0) === 0;
    }

    public function isPositive(): bool
    {
        return bccomp((string) $this->amount, '0', 0) === 1;
    }

    public function isNegative(): bool
    {
        return bccomp((string) $this->amount, '0', 0) === -1;
    }

    public function isSameCurrency(Money $other): bool
    {
        return $this->currency->equals($other->currency);
    }

    public function isDivisibleBy(int $divisor): bool
    {
        return bcmod((string) $this->amount, (string) $divisor) === '0';
    }

    public function getAmount(): int
    {
        return (int) $this->amount;
    }

    /**
     * Alias for getAmount()
     */
    public function getMinorAmount(): int
    {
        return (int) $this->amount;
    }

    public function getMajorAmount(): float
    {
        $subunit = $this->currency->getSubunit();

        return (float) bcdiv((string) $this->amount, (string) $subunit, $this->currency->getMinorUnit());
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function format(?string $locale = null): string
    {
        $formatter = new MoneyFormatter();

        return $formatter->format($this, $locale);
    }

    /**
     * Format as simple string (e.g., $100.00)
     */
    public function formatSimple(): string
    {
        $formatter = new MoneyFormatter();

        return $formatter->formatSimple($this);
    }

    /**
     * Format with specific decimal places
     */
    public function formatByDecimal(int $decimals = 2): string
    {
        $formatter = new MoneyFormatter();

        return $formatter->formatByDecimal($this, $decimals);
    }

    public function formatWithoutSymbol(): string
    {
        $formatter = new MoneyFormatter();

        return $formatter->formatWithoutSymbol($this);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'amount' => (int) $this->amount,
            'currency' => $this->currency->getCode(),
            'formatted' => $this->formatSimple(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert to string
     */
    public function toString(): string
    {
        return $this->formatSimple();
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function toDatabaseValue(): int
    {
        return (int) $this->amount;
    }

    public static function sum(array $moneys): self
    {
        if (empty($moneys)) {
            throw new InvalidArgumentException('Cannot sum empty array');
        }

        $first = reset($moneys);
        $result = $first;

        foreach (array_slice($moneys, 1) as $money) {
            $result = $result->add($money);
        }

        return $result;
    }

    /**
     * Average multiple money objects
     */
    public static function average(array $moneys): self
    {
        $sum = self::sum($moneys);

        return $sum->divide(count($moneys));
    }

    public static function min(Money ...$moneys): self
    {
        if (empty($moneys)) {
            throw new InvalidArgumentException('Cannot get min of empty array');
        }

        $min = $moneys[0];

        foreach (array_slice($moneys, 1) as $money) {
            if ($money->lessThan($min)) {
                $min = $money;
            }
        }

        return $min;
    }

    public static function max(Money ...$moneys): self
    {
        if (empty($moneys)) {
            throw new InvalidArgumentException('Cannot get max of empty array');
        }

        $max = $moneys[0];

        foreach (array_slice($moneys, 1) as $money) {
            if ($money->greaterThan($max)) {
                $max = $money;
            }
        }

        return $max;
    }

    private function assertSameCurrency(Money $other): void
    {
        if (! $this->isSameCurrency($other)) {
            throw CurrencyMismatchException::create(
                $this->currency->getCode(),
                $other->currency->getCode()
            );
        }
    }

    private function round(string $amount, int $roundingMode): string
    {
        if (str_contains($amount, '.')) {
            [$integer, $fraction] = explode('.', $amount);
            $fraction = substr($fraction, 0, 1);
            $isNegative = str_starts_with($amount, '-');

            return match ($roundingMode) {
                RoundingMode::HALF_UP => bcadd($amount, $isNegative ? '-0.5' : '0.5', 0),
                RoundingMode::HALF_DOWN => bcadd($amount, $isNegative ? '-0.4999999' : '0.4999999', 0),
                RoundingMode::HALF_EVEN => (string) round((float) $amount, 0, PHP_ROUND_HALF_EVEN),
                RoundingMode::CEILING => $isNegative ? $integer : bcadd($amount, '0.9999999', 0),
                RoundingMode::FLOOR => $isNegative ? bcadd($amount, '-0.9999999', 0) : $integer,
                RoundingMode::UP => bcadd($amount, $isNegative ? '-0.9999999' : '0.9999999', 0),
                RoundingMode::DOWN => $integer,
                default => bcadd($amount, $isNegative ? '-0.5' : '0.5', 0),
            };
        }

        return $amount;
    }
}
