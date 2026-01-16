<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * AnalyticsManagerService for the Ally package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Services;

use Ally\Models\Reseller;
use Database\DB;
use Helpers\File\Cache;
use Wallet\Services\WalletManagerService;

class AnalyticsManagerService
{
    /**
     * The aggregation interval.
     */
    protected string $interval = 'daily';

    /**
     * The client ID scope.
     */
    protected ?string $clientId = null;

    /**
     * The reseller/owner ID scope.
     */
    protected ?string $resellerId = null;

    public function __construct(
        private readonly WalletManagerService $walletManager
    ) {
    }

    public function daily(): self
    {
        $this->interval = 'daily';

        return $this;
    }

    public function monthly(): self
    {
        $this->interval = 'monthly';

        return $this;
    }

    public function yearly(): self
    {
        $this->interval = 'yearly';

        return $this;
    }

    public function forClient(int|string $id): self
    {
        $this->clientId = (string) $id;

        return $this;
    }

    /**
     * Scope analytics to a specific reseller/owner.
     */
    public function forReseller(int|string $id): self
    {
        $this->resellerId = (string) $id;

        return $this;
    }

    protected function getDateFormat(): string
    {
        return match ($this->interval) {
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };
    }

    /**
     * Reset fluent options to defaults.
     */
    protected function resetFluentOptions(): void
    {
        $this->interval = 'daily';
        $this->clientId = null;
        $this->resellerId = null;
    }

    public function topResellers(int $limit = 5, ?string $start = null, ?string $end = null): array
    {
        $cacheKey = "ally_analytics_top_resellers_{$limit}_" . ($start ?? 'all') . '_' . ($end ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($limit, $start, $end) {
            $query = Reseller::query()
                ->orderBy('id', 'DESC')
                ->limit($limit);

            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }

            return $query->get()->toArray();
        });
    }

    public function totalCreditsDistributed(): int
    {
        return Cache::create()->remember('ally_analytics_total_credits', 3600, function () {
            return (int) DB::table('wallet')
                ->where('owner_type', Reseller::class)
                ->sum('balance');
        });
    }

    /**
     * Get tier-based distribution of partners.
     */
    public function tierSnapshot(?string $start = null, ?string $end = null): array
    {
        $cacheKey = 'ally_analytics_tier_snapshot_' . ($start ?? 'all') . '_' . ($end ?? 'all') . '_' . ($this->resellerId ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($start, $end) {
            $query = Reseller::query();

            if ($this->resellerId) {
                $query->where('id', $this->resellerId);
            }

            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }

            $stats = $query->select('tier')
                ->selectRaw('count(*) as count')
                ->groupBy('tier')
                ->get();

            $snapshot = [];
            foreach ($stats as $row) {
                $snapshot[$row->tier] = $row->count;
            }

            $this->resetFluentOptions();

            return $snapshot;
        });
    }

    public function resellerStats(Reseller|int|string $reseller): array
    {
        if (! $reseller instanceof Reseller) {
            $reseller = Reseller::find($reseller);
        }

        if (! $reseller) {
            return [];
        }

        $wallet = $this->walletManager->findByOwner($reseller->id, Reseller::class, 'USD');

        return [
            'balance' => $wallet ? $this->walletManager->getBalance($wallet->id)->getAmount() : 0,
            'tier' => $reseller->tier->value,
            'total_spend' => 0, // Placeholder: Would need transaction history sum
            'licenses_active' => 0, // Placeholder: Would need Client/License count
        ];
    }

    public function creditTrends(string $start, string $end, ?string $interval = null): array
    {
        $this->interval = $interval ?? $this->interval;
        $cacheKey = "ally_analytics_credit_trends_{$start}_{$end}_{$this->interval}_" . ($this->resellerId ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($start, $end) {
            $format = $this->getDateFormat();

            $query = DB::table('wallet_transaction')
                ->join('wallet', 'wallet.id', '=', 'wallet_transaction.wallet_id')
                ->where('wallet.owner_type', Reseller::class)
                ->where('wallet_transaction.type', 'credit')
                ->where('wallet_transaction.status', 'COMPLETED');

            if ($this->resellerId) {
                $query->where('wallet.owner_id', $this->resellerId);
            }

            $results = $query
                ->whereBetween('wallet_transaction.created_at', [$start, $end])
                ->select(DB::raw("DATE_FORMAT(wallet_transaction.created_at, '{$format}') as date"))
                ->selectRaw('SUM(wallet_transaction.amount) as total_amount')
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            $trends = [];
            foreach ($results as $row) {
                $trends[(string) $row['date']] = (int) $row['total_amount'];
            }

            $this->resetFluentOptions();

            return $trends;
        });
    }
}
