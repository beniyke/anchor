<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Trait for users with referral capabilities.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Traits;

use Database\Relations\HasMany;
use Database\Relations\HasOne;
use Refer\Models\Referral;
use Refer\Models\ReferralCode;
use Refer\Refer;

trait HasReferrals
{
    public function referralCode(): HasOne
    {
        return $this->hasOne(ReferralCode::class, 'user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy(): HasOne
    {
        return $this->hasOne(Referral::class, 'referee_id');
    }

    public function getReferralCode(): ReferralCode
    {
        return Refer::generateCode($this->id);
    }

    public function getReferralCodeAttribute(): string
    {
        return $this->getReferralCode()->code;
    }

    public function getReferralLink(?string $baseUrl = null): string
    {
        $code = $this->getReferralCode();
        $baseUrl = $baseUrl ?? (config('app.url') . '/register');

        return $baseUrl . '?ref=' . $code->code;
    }

    public function getReferralStats(): array
    {
        return Refer::getStats($this->id);
    }

    public function wasReferred(): bool
    {
        return $this->referredBy()->exists();
    }

    public function getReferrer(): ?self
    {
        $referral = $this->referredBy()->first();

        return $referral?->referrer;
    }
}
