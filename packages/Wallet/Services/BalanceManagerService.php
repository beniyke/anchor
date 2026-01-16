<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Balance Manager Service
 *
 * Handles balance calculations, reconciliation, and locking
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services;

use Database\DB;
use Exception;
use Money\Money;
use Wallet\Exceptions\WalletNotFoundException;
use Wallet\Models\Wallet;

class BalanceManagerService
{
    public function getBalance(int $walletId): Money
    {
        $wallet = Wallet::find($walletId);

        if (! $wallet) {
            throw new WalletNotFoundException("wallet_id:{$walletId}");
        }

        return $wallet->getBalanceAsMoney();
    }

    /**
     * Calculate balance from transaction ledger (source of truth)
     */
    public function calculateBalanceFromLedger(int $walletId): Money
    {
        $wallet = Wallet::find($walletId);

        if (! $wallet) {
            throw new WalletNotFoundException("wallet_id:{$walletId}");
        }

        return $wallet->calculateBalanceFromLedger();
    }

    /**
     * Reconcile cached balance with ledger
     *
     * @return bool True if balanced match
     *
     * @throws Exception if mismatch found
     */
    public function reconcile(int $walletId): bool
    {
        $executeReconcile = function () use ($walletId) {
            $wallet = $this->lockWalletForUpdate($walletId);

            $cachedBalance = $wallet->getBalanceAsMoney();
            $ledgerBalance = $this->calculateBalanceFromLedger($walletId);

            if (! $cachedBalance->equals($ledgerBalance)) {
                logger('wallet.log')->error('Balance reconciliation mismatch', [
                    'wallet_id' => $walletId,
                    'cached_balance' => $cachedBalance->getAmount(),
                    'ledger_balance' => $ledgerBalance->getAmount(),
                    'difference' => $cachedBalance->subtract($ledgerBalance)->getAmount(),
                ]);

                // Auto-fix: update cached balance from ledger
                $wallet->balance = $ledgerBalance->getAmount();
                $wallet->save();

                return false;
            }

            return true;
        };

        if (DB::connection()->inTransaction()) {
            return $executeReconcile();
        }

        return DB::transaction($executeReconcile);
    }

    /**
     * Lock wallet row for update (prevents concurrent modifications)
     *
     * MUST be called within a database transaction
     */
    public function lockWalletForUpdate(int $walletId): Wallet
    {
        $wallet = DB::table('wallet')
            ->where('id', $walletId)
            ->lockForUpdate()
            ->first();

        if (! $wallet) {
            throw new WalletNotFoundException("wallet_id:{$walletId}");
        }

        // Convert stdClass to Wallet model
        return Wallet::find($walletId);
    }
}
