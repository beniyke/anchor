<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Money allocator.
 * Handles splitting money into parts with proper remainder distribution.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

class Allocator
{
    public function allocate(Money $money, array $ratios): array
    {
        $total = (int) array_sum($ratios);
        $remainder = (int) $money->getAmount();
        $results = [];

        foreach ($ratios as $ratio) {
            $share = (int) bcdiv(
                bcmul((string) $money->getAmount(), (string) $ratio, 0),
                (string) $total,
                0
            );

            $results[] = Money::make($share, $money->getCurrency());
            $remainder -= $share;
        }

        for ($i = 0; $i < $remainder; $i++) {
            $results[$i] = $results[$i]->add(Money::make(1, $money->getCurrency()));
        }

        return $results;
    }

    /**
     * Allocate equally to N parts
     */
    public function allocateTo(Money $money, int $n): array
    {
        return $this->allocate($money, array_fill(0, $n, 1));
    }
}
