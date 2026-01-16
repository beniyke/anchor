<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Plan Manager for handling subscription plans.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Wave\Models\Plan;
use Wave\Models\Product;
use Wave\Services\Builders\PlanBuilder;

class PlanManagerService
{
    public function make(): PlanBuilder
    {
        return new PlanBuilder($this);
    }

    public function find(string|int $id): ?Plan
    {
        if (is_numeric($id)) {
            $plan = Plan::find($id);
            if ($plan) {
                return $plan;
            }
        }

        $plan = Plan::query()->where('refid', $id)->first();
        if ($plan) {
            return $plan;
        }

        return Plan::query()->where('slug', $id)->first();
    }

    public function findProduct(string|int $id): ?Product
    {
        if (is_int($id)) {
            return Product::find($id);
        }

        return Product::query()->where('refid', $id)->first();
    }

    public function createPlan(array $data): Plan
    {
        return Plan::create($data);
    }
}
