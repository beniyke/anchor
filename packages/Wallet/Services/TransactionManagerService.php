<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Manager Service
 *
 * Handles all transaction operations with atomicity and idempotency
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use PDOException;
use UnitEnum;
use Wallet\Enums\TransactionStatus;
use Wallet\Enums\TransactionType;
use Wallet\Exceptions\DuplicateTransactionException;
use Wallet\Exceptions\InsufficientFundsException;
use Wallet\Models\Transaction;

class TransactionManagerService
{
    public function __construct(
        private readonly BalanceManagerService $balanceManager,
        private readonly FeeCalculatorService $feeCalculator
    ) {
    }

    /**
     * @throws InsufficientFundsException
     * @throws DuplicateTransactionException
     */
    public function createTransaction(array $data): Transaction
    {
        if (isset($data['reference_id'])) {
            $existing = $this->getTransactionByReference($data['reference_id']);
            if ($existing) {
                throw new DuplicateTransactionException($data['reference_id']);
            }
        }

        if (isset($data['idempotency_key'])) {
            $existing = DB::table('wallet_transaction')
                ->where('idempotency_key', $data['idempotency_key'])
                ->first();
            if ($existing) {
                throw new DuplicateTransactionException($data['idempotency_key']);
            }
        }

        $executeTransaction = function () use ($data) {
            $wallet = $this->balanceManager->lockWalletForUpdate($data['wallet_id']);

            $balanceBefore = $wallet->balance;

            $netAmount = $this->calculateNetAmount($data);

            if (in_array($data['type'], [TransactionType::DEBIT->value, TransactionType::TRANSFER_OUT->value])) {
                if ($balanceBefore < abs($netAmount)) {
                    throw new InsufficientFundsException(
                        $data['wallet_id'],
                        abs($netAmount),
                        $balanceBefore
                    );
                }
            }

            $balanceAfter = $balanceBefore + $netAmount;
            $status = $data['status'] ?? TransactionStatus::COMPLETED;
            $now = DateTimeHelper::now()->toDateTimeString();

            try {
                $transaction = Transaction::create([
                    'wallet_id' => $data['wallet_id'],
                    'type' => $data['type'],
                    'refid' => $data['refid'] ?? null,
                    'amount' => $data['amount'] ?? 0,
                    'fee' => $data['fee'] ?? 0,
                    'net_amount' => $netAmount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_id' => $data['reference_id'],
                    'idempotency_key' => $data['idempotency_key'] ?? null,
                    'parent_transaction_id' => $data['parent_transaction_id'] ?? null,
                    'payment_processor' => $data['payment_processor'] ?? null,
                    'processor_transaction_id' => $data['processor_transaction_id'] ?? null,
                    'processor_fee' => $data['processor_fee'] ?? 0,
                    'description' => $data['description'] ?? null,
                    'metadata' => $data['metadata'] ?? null,
                    'status' => $status,
                    'created_at' => $now,
                    'completed_at' => $status === TransactionStatus::COMPLETED ? $now : null,
                ]);
            } catch (PDOException $e) {
                if (
                    str_contains($e->getMessage(), 'UNIQUE constraint failed') ||
                    str_contains($e->getMessage(), 'Duplicate entry')
                ) {
                    throw new DuplicateTransactionException(
                        $data['reference_id'] ?? $data['idempotency_key'] ?? 'unknown'
                    );
                }
                throw $e;
            }

            $wallet->updateBalance($netAmount);

            return $transaction;
        };

        if (DB::connection()->inTransaction()) {
            return $executeTransaction();
        }

        return DB::transaction($executeTransaction);
    }

    public function getTransactionByReference(string $referenceId): ?Transaction
    {
        return Transaction::query()->where('reference_id', $referenceId)->first();
    }

    public function getWalletTransactions(int $walletId, array $filters = []): iterable
    {
        $query = Transaction::query()->where('wallet_id', $walletId);

        if (isset($filters['type'])) {
            $type = $filters['type'] instanceof UnitEnum ? $filters['type']->value : $filters['type'];
            $query->where('type', $type);
        }

        if (isset($filters['status'])) {
            $status = $filters['status'] instanceof UnitEnum ? $filters['status']->value : $filters['status'];
            $query->where('status', $status);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    private function calculateNetAmount(array $data): int
    {
        $amount = $data['amount'] ?? 0;
        $fee = $data['fee'] ?? 0;

        return match ($data['type']) {
            TransactionType::CREDIT->value, TransactionType::TRANSFER_IN->value => $amount - $fee,  // Add net to balance
            TransactionType::DEBIT->value, TransactionType::TRANSFER_OUT->value => - ($amount + $fee), // Remove from balance (negative)
            TransactionType::REFUND->value => -$amount + $fee,  // Reverse original transaction (negative amount, add back fee)
            default => 0,
        };
    }
}
