<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Coupon Manager for handling discounts and promotions.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Wave\Enums\CouponType;
use Wave\Exceptions\CouponExpiredException;
use Wave\Models\Coupon;
use Wave\Models\Discount;
use Wave\Models\Invoice;
use Wave\Models\Subscription;
use Wave\Services\Builders\CouponBuilder;

class CouponManagerService
{
    /**
     * Start a fluent coupon builder
     */
    public function make(): CouponBuilder
    {
        return new CouponBuilder();
    }

    public function findByCode(string $code): ?Coupon
    {
        return Coupon::query()->where('code', $code)->first();
    }

    /**
     * Apply a coupon to a subscription
     */
    public function applyToSubscription(Subscription $subscription, string $code): void
    {
        $coupon = $this->findByCode($code);
        if (!$coupon || !$coupon->isValid()) {
            throw new CouponExpiredException($code);
        }

        Discount::create([
            'owner_id' => $subscription->owner_id,
            'owner_type' => $subscription->owner_type,
            'subscription_id' => $subscription->id,
            'coupon_id' => $coupon->id,
            'amount_saved' => 0, // Calculated during invoicing
        ]);

        $coupon->update(['times_redeemed' => $coupon->times_redeemed + 1]);
    }

    /**
     * Apply recurring discounts to an invoice from a subscription
     */
    public function applyRecurringDiscounts(Subscription $subscription, Invoice $invoice): void
    {
        $discounts = Discount::query()->where('subscription_id', $subscription->id)->get();

        foreach ($discounts as $discount) {
            $coupon = $discount->coupon;
            if (!$coupon || !$coupon->isValid()) {
                continue;
            }

            $savings = $this->calculateSavings($coupon, $invoice->amount);

            $discount->update(['amount_saved' => $discount->amount_saved + $savings]);

            $invoice->update([
                'amount' => $invoice->amount - $savings,
                'total' => $invoice->total - $savings,
            ]);
        }
    }

    public function calculateSavings(Coupon $coupon, int $amount): int
    {
        if ($coupon->type === CouponType::PERCENT) {
            return (int) ($amount * ($coupon->value / 10000)); // Value stored in basis points or minor units
        }

        return (int) $coupon->value;
    }
}
