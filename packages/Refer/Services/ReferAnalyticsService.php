<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Refer package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Services;

use Helpers\DateTimeHelper;
use Refer\Enums\ReferralStatus;
use Refer\Models\Referral;
use Refer\Models\ReferralCode;

class ReferAnalyticsService
{
    public function getOverview(): array
    {
        return [
            'total_codes' => ReferralCode::count(),
            'active_codes' => ReferralCode::where('is_active', true)->count(),
            'total_referrals' => Referral::count(),
            'pending_referrals' => Referral::where('status', ReferralStatus::PENDING)->count(),
            'rewarded_referrals' => Referral::where('status', ReferralStatus::REWARDED)->count(),
            'total_rewards_paid' => Referral::where('status', ReferralStatus::REWARDED)->sum('referrer_reward') ?? 0,
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    public function getTopReferrers(int $limit = 10): array
    {
        $referrers = Referral::selectRaw('referrer_id, COUNT(*) as referral_count, SUM(referrer_reward) as total_earnings')
            ->where('status', ReferralStatus::REWARDED)
            ->groupBy('referrer_id')
            ->orderBy('referral_count', 'desc')
            ->limit($limit)
            ->get();

        return $referrers->all();
    }

    public function getDailyTrends(int $days = 30): array
    {
        $trends = [];
        $startDate = DateTimeHelper::now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i)->format('Y-m-d');
            $nextDate = $startDate->addDays($i + 1)->format('Y-m-d');

            $dayQuery = Referral::where('created_at', '>=', $date)->where('created_at', '<', $nextDate);

            $trends[] = [
                'date' => $date,
                'referrals' => (clone $dayQuery)->count(),
                'rewarded' => (clone $dayQuery)->where('status', ReferralStatus::REWARDED)->count(),
            ];
        }

        return $trends;
    }

    public function getByStatus(): array
    {
        $result = [];

        foreach (ReferralStatus::cases() as $status) {
            $result[$status->value] = Referral::where('status', $status)->count();
        }

        return $result;
    }

    public function getCodeUsageStats(): array
    {
        $codes = ReferralCode::get();

        return [
            'total_codes' => $codes->count(),
            'codes_with_referrals' => $codes->filter(fn ($c) => $c->uses_count > 0)->count(),
            'avg_referrals_per_code' => $codes->avg('uses_count') ?? 0,
            'max_referrals' => $codes->max('uses_count') ?? 0,
        ];
    }

    public function getMonthlyRewards(int $months = 12): array
    {
        $result = [];
        $startDate = DateTimeHelper::now()->subMonths($months);

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->addMonths($i)->startOfMonth()->format('Y-m-d');
            $monthEnd = $startDate->addMonths($i)->endOfMonth()->format('Y-m-d');

            $monthQuery = Referral::where('rewarded_at', '>=', $monthStart)
                ->where('rewarded_at', '<=', $monthEnd)
                ->where('status', ReferralStatus::REWARDED);

            $result[] = [
                'month' => $startDate->addMonths($i)->format('Y-m'),
                'count' => (clone $monthQuery)->count(),
                'total_rewards' => (clone $monthQuery)->sum('referrer_reward') ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Calculate conversion rate (referrals that got rewarded).
     */
    private function calculateConversionRate(): float
    {
        $total = Referral::count();

        if ($total === 0) {
            return 0.0;
        }

        $rewarded = Referral::where('status', ReferralStatus::REWARDED)->count();

        return round(($rewarded / $total) * 100, 2);
    }
}
