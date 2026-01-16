<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics Manager
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use DateTimeInterface;
use Helpers\Array\Collections;
use Helpers\DateTimeHelper;
use Money\Money;
use Wave\Enums\InvoiceStatus;
use Wave\Enums\SubscriptionStatus;
use Wave\Models\Affiliate;
use Wave\Models\Coupon;
use Wave\Models\Invoice;
use Wave\Models\Referral;
use Wave\Models\Subscription;

class AnalyticsManagerService
{
    /**
     * Calculate Monthly Recurring Revenue (MRR) - Optimized
     *
     * Uses aggregation to avoid loading all subscriptions.
     * Returns Money object.
     */
    public function mrr(): Money
    {
        // Get active subscriptions grouped by plan with quantity sum
        // Use DB join to ensure we get plan details for aggregation
        $plans = Subscription::query()
            ->join('wave_plan', 'wave_subscription.plan_id', '=', 'wave_plan.id')
            ->selectRaw('wave_subscription.plan_id, sum(wave_subscription.quantity) as total_quantity, wave_plan.price, wave_plan.interval, wave_plan.interval_count')
            ->where('wave_subscription.status', SubscriptionStatus::ACTIVE->value)
            ->groupBy('wave_subscription.plan_id', 'wave_plan.price', 'wave_plan.interval', 'wave_plan.interval_count')
            ->get();

        $mrr = 0;

        foreach ($plans as $group) {
            $price = $group->price;
            $interval = $group->interval;
            $count = $group->interval_count ?? 1;
            if ($count < 1) {
                $count = 1;
            }

            // Normalize to monthly
            // Interval comes from DB join, so it might be string or Enum depending on cast.
            // Since we use join, it's likely raw string unless we hydrate Plan model?
            // query() on Subscription returns Subscription models (or partials).
            // But 'interval' is on Plan table. Subscription model doesn't cast plain joined fields unless configured.
            // So assume string.
            $intervalValue = is_object($interval) ? $interval->value : $interval;

            $monthlyPrice = match ($intervalValue) {
                'year' => $price / 12,
                'month' => $price,
                'week' => $price * 52 / 12,
                'day' => $price * 365 / 12,
                default => $price,
            };

            // Add to total: (Monthly Price / Interval Count) * Total Quantity
            $mrr += (int) (($monthlyPrice / $count) * $group->total_quantity);
        }

        return Money::cents($mrr, config('wave.currency', 'USD'));
    }

    /**
     * Get historical data for charts
     *
     * @param string       $metric 'revenue', 'new_subscriptions', 'cancellations'
     * @param array|string $range  '7d', '30d', 'year', or [start, end]
     *
     * @return array ['labels' => [], 'values' => []]
     */
    public function getHistory(?string $metric = null, string|array $range = '30d'): array|AnalyticsHistoryProxyService
    {
        if ($metric === null) {
            return new AnalyticsHistoryProxyService($this);
        }
        [$start, $end] = $this->parseRange($range);
        $labels = [];
        $values = [];

        // Use Carbon's daysUntil via DateTimeHelper (since it extends Carbon)
        $period = DateTimeHelper::parse($start)->daysUntil($end);

        // Pre-fill labels and values
        foreach ($period as $date) {
            $labels[] = $date->format('M d'); // e.g. Jan 01
            $values[$date->format('Y-m-d')] = 0;
        }

        // Format for query
        $startStr = $start->format('Y-m-d H:i:s');
        $endStr = $end->format('Y-m-d H:i:s');

        // Query Data
        // Query Data via PHP grouping for robustness (SQLite/MySQL date diffs)
        $groupByDate = function ($item, $field) {
            $val = $item->{$field};
            if (empty($val)) {
                return 'unknown';
            }
            if ($val instanceof DateTimeInterface) {
                return $val->format('Y-m-d');
            }
            if (is_string($val)) {
                return substr($val, 0, 10);
            }

            return 'unknown';
        };

        $queryData = [];

        if ($metric === 'revenue') {
            $groups = Invoice::query()
                ->where('status', InvoiceStatus::PAID->value)
                ->whereBetween('paid_at', [$startStr, $endStr])
                ->get()
                ->groupBy(fn ($item) => $groupByDate($item, 'paid_at'));

            foreach ($groups as $key => $group) {
                $queryData[$key] = Collections::make($group)->pluck('total')->sum();
            }
        } elseif ($metric === 'new_subscriptions') {
            $groups = Subscription::query()
                ->whereBetween('created_at', [$startStr, $endStr])
                ->get()
                ->groupBy(fn ($item) => $groupByDate($item, 'created_at'));

            foreach ($groups as $key => $group) {
                // arr() handles count too (via Countable interface on Collections)
                $queryData[$key] = arr($group)->count();
            }
        } elseif ($metric === 'cancellations') {
            $groups = Subscription::query()
                ->whereBetween('canceled_at', [$startStr, $endStr])
                ->get()
                ->groupBy(fn ($item) => $groupByDate($item, 'canceled_at'));

            foreach ($groups as $key => $group) {
                $queryData[$key] = arr($group)->count();
            }
        }

        $data = $queryData;

        // Merge Data
        foreach ($data as $date => $val) {
            if (isset($values[$date])) {
                $values[$date] = (int) $val; // Ensure int/float
            }
        }

        return [
            'labels' => $labels,
            'values' => array_values($values)
        ];
    }

    public function productStats(): array
    {
        // Top Plans by active subscriptions
        $topPlans = Subscription::query()
            ->selectRaw('plan_id, count(*) as count')
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->groupBy('plan_id')
            ->orderBy('count', 'desc')
            ->with('plan')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'name' => $item->plan->name ?? 'Unknown',
                'count' => $item->count
            ]);

        return [
            'top_plans' => $topPlans
        ];
    }

    public function couponStats(): array
    {
        return [
            'total_redemptions' => Coupon::sum('times_redeemed'),
            'active_coupons' => Coupon::where('status', 'active')->count(),
        ];
    }

    public function invoiceStats(): array
    {
        return [
            'paid_count' => Invoice::paid()->count(),
            'unpaid_count' => Invoice::unpaid()->count(),
            'overdue_count' => Invoice::overdue()->count(),
            'total_collected' => Money::cents(Invoice::paid()->sum('total'), config('wave.currency', 'USD')),
        ];
    }

    public function subscriberStats(int|string $ownerId, string $ownerType): array
    {
        // Use owner_id as standard for Wave models
        $ltv = Invoice::paid()
            ->owner($ownerId)
            ->where('owner_type', $ownerType)
            ->sum('total');

        $activeSubs = Subscription::isActive()
            ->owner($ownerId)
            ->where('owner_type', $ownerType)
            ->count();

        return [
            'ltv' => Money::cents($ltv, config('wave.currency', 'USD')),
            'active_subscriptions' => $activeSubs
        ];
    }

    /**
     * Helper to parse range string
     */
    private function parseRange(string|array $range): array
    {
        if (is_array($range)) {
            // [start, end]
            return [
                DateTimeHelper::parse($range[0]),
                DateTimeHelper::parse($range[1])
            ];
        }

        $end = DateTimeHelper::now()->endOfDay();
        $start = match ($range) {
            '7d' => DateTimeHelper::now()->subDays(7)->startOfDay(),
            '30d' => DateTimeHelper::now()->subDays(30)->startOfDay(),
            '90d' => DateTimeHelper::now()->subDays(90)->startOfDay(),
            '12m', 'year' => DateTimeHelper::now()->subMonths(12)->startOfDay(),
            default => DateTimeHelper::now()->subDays(30)->startOfDay(),
        };

        return [$start, $end];
    }

    /**
     * Calculate Total Revenue within a date range (based on paid invoices)
     */
    public function revenue(?string $startDate = null, ?string $endDate = null): Money
    {
        $query = Invoice::where('status', InvoiceStatus::PAID->value);

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }

        return Money::cents((int) $query->sum('total'), config('wave.currency', 'USD'));
    }

    /**
     * Count Active Subscribers
     */
    public function activeSubscribers(): int
    {
        return Subscription::whereIn('status', [
            SubscriptionStatus::ACTIVE->value,
            SubscriptionStatus::TRIALING->value
        ])->count();
    }

    /**
     * Count subscribers currently on trial
     */
    public function trialingSubscribers(): int
    {
        return Subscription::where('status', SubscriptionStatus::TRIALING->value)->count();
    }

    public function churnRate(int $days = 30): float
    {
        $startDate = DateTimeHelper::now()->subDays($days);

        $canceledInPeriod = Subscription::where('canceled_at', '>=', $startDate)->count();
        $currentActive = $this->activeSubscribers();

        // New subscriptions in last X days shouldn't count towards "Start Active"
        $newInPeriod = Subscription::where('created_at', '>=', $startDate)->count();

        $startActive = $currentActive + $canceledInPeriod - $newInPeriod;

        if ($startActive <= 0) {
            return 0.0;
        }

        return round(($canceledInPeriod / $startActive) * 100, 2);
    }

    /**
     * 10. Affiliate Statistics
     *
     * Retrieves high-level affiliate performance metrics.
     */
    public function affiliateStats(?string $affiliateCode = null): array
    {
        if ($affiliateCode) {
            $affiliate = Affiliate::query()->where('code', $affiliateCode)->first();
            if (!$affiliate) {
                return [
                    'total_conversions' => 0,
                    'total_commission' => Money::cents(0, config('wave.currency', 'USD')),
                ];
            }

            $referrals = Referral::query()
                ->where('affiliate_id', $affiliate->id)
                ->where('status', 'converted')
                ->get();

            $commissionCents = Collections::make($referrals)->pluck('commission_amount')->sum();

            return [
                'total_conversions' => count($referrals),
                'total_commission' => Money::cents($commissionCents, config('wave.currency', 'USD')),
            ];
        }

        return [
            'active_affiliates' => Affiliate::query()->where('status', 'active')->count(),
            'total_conversions' => Referral::query()->where('status', 'converted')->count(),
            'total_commission' => Money::cents((int) Referral::query()->where('status', 'converted')->sum('commission_amount'), config('wave.currency', 'USD')),
        ];
    }
}
