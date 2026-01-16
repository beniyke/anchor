<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tax Manager
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Wave\Models\TaxRate;
use Wave\Services\Builders\TaxRateBuilder;

class TaxManagerService
{
    /**
     * Start a fluent tax rate builder
     */
    public function make(): TaxRateBuilder
    {
        return new TaxRateBuilder();
    }

    /**
     * Find a tax rate by id or refid
     */
    public function find(string|int $id): ?TaxRate
    {
        if (is_int($id)) {
            return TaxRate::find($id);
        }

        return TaxRate::query()->where('refid', $id)->first();
    }
}
