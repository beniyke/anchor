<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Analytics Service
 *
 * Provides reporting and analytics for wallet transactions
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services;

use Database\DB;
use Money\Money;
use Wallet\Enums\TransactionStatus;
use Wallet\Enums\TransactionType;

class WalletAnalyticsService
{
    public function getTotalCredits(int $walletId, ?string $from = null, ?string $to = null): Money
    {
        return $this->getTotalByType($walletId, TransactionType::CREDIT->value, $from, $to);
    }

    public function getTotalDebits(int $walletId, ?string $from = null, ?string $to = null): Money
    {
        return $this->getTotalByType($walletId, TransactionType::DEBIT->value, $from, $to);
    }

    public function getTotalByType(int $walletId, string $type, ?string $from = null, ?string $to = null): Money
    {
        $wallet = DB::table('wallet')->where('id', $walletId)->first();
        $currency = $wallet->currency ?? 'USD';

        $query = DB::table('wallet_transaction')
            ->where('wallet_id', $walletId)
            ->where('type', $type)
            ->where('status', TransactionStatus::COMPLETED->value);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        $total = (int) $query->sum('amount');

        return Money::make($total, $currency);
    }

    public function getTransactionCount(int $walletId, ?string $type = null, ?string $from = null, ?string $to = null): int
    {
        $query = DB::table('wallet_transaction')
            ->where('wallet_id', $walletId)
            ->where('status', TransactionStatus::COMPLETED->value);

        if ($type) {
            $query->where('type', $type);
        }
        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return (int) $query->count();
    }

    public function getDailyVolume(int $walletId, string $from, string $to): array
    {
        $wallet = DB::table('wallet')->where('id', $walletId)->first();
        $currency = $wallet->currency ?? 'USD';

        $query = DB::table('wallet_transaction')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("SUM(CASE WHEN type IN ('credit', 'transfer_in') THEN amount ELSE 0 END) as credits"),
                DB::raw("SUM(CASE WHEN type IN ('debit', 'transfer_out') THEN amount ELSE 0 END) as debits"),
                DB::raw("SUM(net_amount) as net")
            )
            ->where('wallet_id', $walletId)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC');

        $results = $query->get();

        return array_map(function ($row) use ($currency) {
            return [
                'date' => $row->date,
                'count' => (int) $row->count,
                'credits' => Money::make((int) $row->credits, $currency),
                'debits' => Money::make((int) $row->debits, $currency),
                'net' => Money::make((int) $row->net, $currency),
            ];
        }, $results);
    }

    public function getMonthlyVolume(int $walletId, string $from, string $to): array
    {
        $wallet = DB::table('wallet')->where('id', $walletId)->first();
        $currency = $wallet->currency ?? 'USD';

        $query = DB::table('wallet_transaction')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw("SUM(CASE WHEN type IN ('credit', 'transfer_in') THEN amount ELSE 0 END) as credits"),
                DB::raw("SUM(CASE WHEN type IN ('debit', 'transfer_out') THEN amount ELSE 0 END) as debits"),
                DB::raw("SUM(net_amount) as net")
            )
            ->where('wallet_id', $walletId)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month', 'ASC');

        $results = $query->get();

        return array_map(function ($row) use ($currency) {
            return [
                'month' => $row->month,
                'count' => (int) $row->count,
                'credits' => Money::make((int) $row->credits, $currency),
                'debits' => Money::make((int) $row->debits, $currency),
                'net' => Money::make((int) $row->net, $currency),
            ];
        }, $results);
    }

    public function getBalanceHistory(int $walletId, string $from, string $to): array
    {
        $wallet = DB::table('wallet')->where('id', $walletId)->first();
        $currency = $wallet->currency ?? 'USD';

        $transactions = DB::table('wallet_transaction')
            ->where('wallet_id', $walletId)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->orderBy('created_at', 'ASC')
            ->get();

        $history = [];
        foreach ($transactions as $tx) {
            $date = substr($tx->created_at, 0, 10);
            // Record the balance_after at end of day
            $history[$date] = [
                'date' => $date,
                'balance' => Money::make((int) $tx->balance_after, $currency),
            ];
        }

        return array_values($history);
    }

    public function getSummary(int $walletId, ?string $from = null, ?string $to = null): array
    {
        $wallet = DB::table('wallet')->where('id', $walletId)->first();
        $currency = $wallet->currency ?? 'USD';

        return [
            'wallet_id' => $walletId,
            'currency' => $currency,
            'current_balance' => Money::make($wallet->balance ?? 0, $currency),
            'total_credits' => $this->getTotalCredits($walletId, $from, $to),
            'total_debits' => $this->getTotalDebits($walletId, $from, $to),
            'transaction_count' => $this->getTransactionCount($walletId, null, $from, $to),
            'credit_count' => $this->getTransactionCount($walletId, TransactionType::CREDIT->value, $from, $to),
            'debit_count' => $this->getTransactionCount($walletId, TransactionType::DEBIT->value, $from, $to),
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    /**
     * Get top spenders (for platform-wide analytics)
     */
    public function getTopSpenders(int $limit = 10, ?string $from = null, ?string $to = null): array
    {
        $query = DB::table('wallet_transaction')
            ->select('wallet_id', DB::raw('SUM(amount) as total_spent'))
            ->where('type', TransactionType::DEBIT->value)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->groupBy('wallet_id')
            ->orderBy('total_spent', 'DESC')
            ->limit($limit);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query->get();
    }

    /**
     * Get platform-wide totals
     *
     * @param string|null $from     Start date
     * @param string|null $to       End date
     * @param string      $currency Currency for Money formatting
     *
     * @return array Platform statistics with Money values
     */
    public function getPlatformTotals(?string $from = null, ?string $to = null, string $currency = 'USD'): array
    {
        $creditQuery = DB::table('wallet_transaction')
            ->where('type', TransactionType::CREDIT->value)
            ->where('status', TransactionStatus::COMPLETED->value);

        $debitQuery = DB::table('wallet_transaction')
            ->where('type', TransactionType::DEBIT->value)
            ->where('status', TransactionStatus::COMPLETED->value);

        if ($from) {
            $creditQuery->where('created_at', '>=', $from);
            $debitQuery->where('created_at', '>=', $from);
        }
        if ($to) {
            $creditQuery->where('created_at', '<=', $to);
            $debitQuery->where('created_at', '<=', $to);
        }

        $totalWallets = (int) DB::table('wallet')->count();
        $totalCredits = (int) $creditQuery->sum('amount');
        $totalDebits = (int) $debitQuery->sum('amount');

        return [
            'total_wallets' => $totalWallets,
            'total_credits' => Money::make($totalCredits, $currency),
            'total_debits' => Money::make($totalDebits, $currency),
            'net_flow' => Money::make($totalCredits - $totalDebits, $currency),
            'period' => ['from' => $from, 'to' => $to],
        ];
    }
}
