<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Refer package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Providers;

use App\Models\User;
use Core\Services\ServiceProvider;
use Database\Relations\HasMany;
use Database\Relations\HasOne;
use Refer\Models\Referral;
use Refer\Models\ReferralCode;
use Refer\Refer;
use Refer\Services\ReferManagerService;

class ReferServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ReferManagerService::class);
    }

    public function boot(): void
    {
        $this->registerUserMacros();
    }

    protected function registerUserMacros(): void
    {
        User::macro('referralCode', function (): HasOne {
            return $this->hasOne(ReferralCode::class, 'user_id');
        });

        User::macro('referrals', function (): HasMany {
            return $this->hasMany(Referral::class, 'referrer_id');
        });

        User::macro('referredBy', function (): HasOne {
            return $this->hasOne(Referral::class, 'referee_id');
        });

        User::macro('getReferralCode', function (): ReferralCode {
            return Refer::generateCode($this->id);
        });

        User::macro('getReferralLink', function (?string $baseUrl = null): string {
            $code = $this->getReferralCode();
            $baseUrl = $baseUrl ?? config('refer.registration_url');

            return $baseUrl . '?ref=' . $code->code;
        });

        User::macro('getReferralStats', function (): array {
            return Refer::getStats($this->id);
        });

        User::macro('wasReferred', function (): bool {
            return $this->referredBy()->exists();
        });

        User::macro('getReferrer', function (): ?User {
            $referral = $this->referredBy()->first();

            return $referral?->referrer;
        });
    }
}
