<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Product Manager
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Wave\Models\Product;
use Wave\Services\Builders\ProductBuilder;

class ProductManagerService
{
    /**
     * Start a fluent product builder
     */
    public function make(): ProductBuilder
    {
        return new ProductBuilder();
    }

    /**
     * Find a product by id or refid
     */
    public function find(string|int $id): ?Product
    {
        if (is_int($id)) {
            return Product::find($id);
        }

        return Product::query()->where('refid', $id)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }
}
