<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Affiliate Manager for handling referrals and commissions.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Exception;
use Wave\Models\Affiliate;
use Wave\Models\Referral;
use Wave\Models\Subscription;

class AffiliateManagerService
{
    public function findByCode(string $code): ?Affiliate
    {
        return Affiliate::query()->where('code', $code)->first();
    }

    /**
     * Record a referral
     */
    public function recordReferral(string $affiliateCode, string|int $referredOwnerId, string $referredOwnerType): Referral
    {
        $affiliate = Affiliate::query()->where('code', $affiliateCode)->first();
        if (!$affiliate) {
            throw new Exception("Invalid affiliate code");
        }

        return Referral::create([
            'affiliate_id' => $affiliate->id,
            'referred_owner_id' => $referredOwnerId,
            'referred_owner_type' => $referredOwnerType,
            'commission_amount' => 0, // Calculated on conversion
            'status' => 'pending',
        ]);
    }

    /**
     * Handle conversion (e.g. after payment)
     */
    public function onConversion(Subscription $subscription): void
    {
        $referral = Referral::query()
            ->where('referred_owner_id', $subscription->owner_id)
            ->where('referred_owner_type', $subscription->owner_type)
            ->where('status', 'pending')
            ->first();

        if ($referral) {
            $rate = config('wave.affiliate.commission_rate', 10) / 100;
            $commission = (int) ($subscription->plan->price * $rate);

            $referral->update([
                'commission_amount' => $commission,
                'status' => 'converted',
            ]);
        }
    }

    public function getReferrals(string $code): array
    {
        $affiliate = $this->findByCode($code);
        if (!$affiliate) {
            return [];
        }

        return Referral::query()
            ->where('affiliate_id', $affiliate->id)
            ->get();
    }
}
