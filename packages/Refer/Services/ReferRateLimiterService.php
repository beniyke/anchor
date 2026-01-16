<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rate limiter for Refer package to prevent abuse.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Services;

use Helpers\DateTimeHelper;
use Refer\Models\Referral;
use Refer\Models\ReferralCode;

class ReferRateLimiterService
{
    public function canUseCode(string $code, ?string $ipAddress = null): bool
    {
        // Check if code exists and is valid
        $referralCode = ReferralCode::findByCode($code);

        if (!$referralCode || !$referralCode->isValid()) {
            return false;
        }

        if ($ipAddress) {
            $recentFromIp = Referral::where('created_at', '>=', DateTimeHelper::now()->subHours(1)->format('Y-m-d H:i:s'))
                ->whereJsonContains('metadata->ip', $ipAddress)
                ->count();

            if ($recentFromIp >= 3) {
                return false;
            }
        }

        return true;
    }

    /**
     * Detect potential fraud patterns.
     */
    public function detectFraud(int $referrerId): array
    {
        $flags = [];
        $referrals = Referral::where('referrer_id', $referrerId)->get();

        if ($referrals->isEmpty()) {
            return $flags;
        }

        // Check for burst of referrals
        $lastHour = DateTimeHelper::now()->subHours(1);
        $recentReferrals = $referrals->filter(fn ($r) => $r->created_at >= $lastHour);

        if ($recentReferrals->count() > 10) {
            $flags[] = 'burst_activity';
        }

        // Check for same IP patterns
        $ips = $referrals->map(fn ($r) => $r->metadata['ip'] ?? null)
            ->filter()
            ->countBy();

        foreach ($ips as $ip => $count) {
            if ($count > 5) {
                $flags[] = 'same_ip_multiple_referrals';
                break;
            }
        }

        return $flags;
    }

    public function validateNotSelfReferral(string $code, int $userId): bool
    {
        $referralCode = ReferralCode::findByCode($code);

        if (!$referralCode) {
            return true;
        }

        return $referralCode->user_id !== $userId;
    }

    public function hasUsedReferralCode(int $userId): bool
    {
        return Referral::where('referee_id', $userId)->exists();
    }
}
