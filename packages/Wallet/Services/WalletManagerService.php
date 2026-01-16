<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Manager Service
 *
 * Main facade for wallet operations
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services;

use Database\DB;
use Money\Currency as MoneyCurrency;
use Money\Money;
use UnitEnum;
use Wallet\Enums\Currency;
use Wallet\Enums\TransactionType;
use Wallet\Exceptions\CurrencyMismatchException;
use Wallet\Exceptions\InvalidAmountException;
use Wallet\Exceptions\TransactionNotFoundException;
use Wallet\Exceptions\WalletNotFoundException;
use Wallet\Models\Transaction;
use Wallet\Models\Wallet;
use Wallet\Services\Builders\FeeRuleBuilder;
use Wallet\Services\Builders\RefundBuilder;
use Wallet\Services\Builders\TransactionBuilder;
use Wallet\Services\Builders\TransactionQueryBuilder;
use Wallet\Services\Builders\WalletBuilder;

class WalletManagerService
{
    public function __construct(
        private readonly TransactionManagerService $transactionManager,
        private readonly BalanceManagerService $balanceManager,
        private readonly FeeCalculatorService $feeCalculator
    ) {
    }

    public function create(int|string $ownerId, string $ownerType, string|Currency $currency = 'USD'): Wallet
    {
        $currencyCode = $currency instanceof Currency ? $currency->value : strtoupper((string) $currency);

        return Wallet::create([
            'owner_id' => $ownerId,
            'owner_type' => $ownerType,
            'balance' => 0,
            'currency' => $currencyCode,
        ]);
    }

    public function createWallet(): WalletBuilder
    {
        return new WalletBuilder();
    }

    public function transaction(int|Wallet $wallet): TransactionBuilder
    {
        $walletModel = $wallet instanceof Wallet ? $wallet : $this->find($wallet);
        if (! $walletModel) {
            $id = $wallet instanceof Wallet ? $wallet->id : $wallet;
            throw new WalletNotFoundException("wallet_id:{$id}");
        }

        return new TransactionBuilder($this, $walletModel);
    }

    public function transactions(int|Wallet $wallet): TransactionQueryBuilder
    {
        return new TransactionQueryBuilder($wallet);
    }

    public function feeRule(string $name): FeeRuleBuilder
    {
        return new FeeRuleBuilder($name);
    }

    public function find(int $walletId): ?Wallet
    {
        return Wallet::find($walletId);
    }

    public function findByOwner(int|string $ownerId, string $ownerType, string|Currency $currency = 'USD'): ?Wallet
    {
        $currencyCode = $currency instanceof Currency ? $currency->value : strtoupper((string) $currency);

        return Wallet::query()
            ->where('owner_id', $ownerId)
            ->where('owner_type', $ownerType)
            ->where('currency', $currencyCode)
            ->first();
    }

    public function getTransactionByReference(string $referenceId): ?Transaction
    {
        return $this->transactionManager->getTransactionByReference($referenceId);
    }

    public function credit(int $walletId, Money $amount, array $metadata = []): Transaction
    {
        $wallet = $this->find($walletId);
        if (! $wallet) {
            throw new WalletNotFoundException("wallet_id:{$walletId}");
        }

        $this->assertCurrencyMatch($wallet, $amount);

        if ($amount->getAmount() <= 0) {
            throw new InvalidAmountException();
        }

        // Calculate fee if requested
        $fee = 0;
        if ($metadata['calculate_fee'] ?? false) {
            $feeData = $this->feeCalculator->calculate(
                TransactionType::CREDIT->value,
                $amount,
                $metadata['payment_processor'] ?? null
            );
            $fee = $feeData['fee']->getAmount();
        } elseif (isset($metadata['fee'])) {
            $fee = is_int($metadata['fee']) ? $metadata['fee'] : $metadata['fee']->getAmount();
        }

        return $this->transactionManager->createTransaction([
            'wallet_id' => $walletId,
            'type' => TransactionType::CREDIT->value,
            'amount' => $amount->getAmount(),
            'fee' => $fee,
            'reference_id' => $metadata['reference_id'] ?? $this->generateReferenceId(),
            'idempotency_key' => $metadata['idempotency_key'] ?? null,
            'payment_processor' => $metadata['payment_processor'] ?? null,
            'processor_transaction_id' => $metadata['processor_transaction_id'] ?? null,
            'processor_fee' => $metadata['processor_fee'] ?? 0,
            'description' => $metadata['description'] ?? 'Credit',
            'metadata' => $metadata['metadata'] ?? null,
        ]);
    }

    public function debit(int $walletId, Money $amount, array $metadata = []): Transaction
    {
        $wallet = $this->find($walletId);
        if (! $wallet) {
            throw new WalletNotFoundException("wallet_id:{$walletId}");
        }

        $this->assertCurrencyMatch($wallet, $amount);

        if ($amount->getAmount() <= 0) {
            throw new InvalidAmountException();
        }

        // Calculate fee if requested
        $fee = 0;
        if ($metadata['calculate_fee'] ?? false) {
            $feeData = $this->feeCalculator->calculate(
                TransactionType::DEBIT->value,
                $amount,
                $metadata['payment_processor'] ?? null
            );
            $fee = $feeData['fee']->getAmount();
        } elseif (isset($metadata['fee'])) {
            $fee = is_int($metadata['fee']) ? $metadata['fee'] : $metadata['fee']->getAmount();
        }

        return $this->transactionManager->createTransaction([
            'wallet_id' => $walletId,
            'type' => TransactionType::DEBIT->value,
            'amount' => $amount->getAmount(),
            'fee' => $fee,
            'reference_id' => $metadata['reference_id'] ?? $this->generateReferenceId(),
            'idempotency_key' => $metadata['idempotency_key'] ?? null,
            'payment_processor' => $metadata['payment_processor'] ?? null,
            'processor_transaction_id' => $metadata['processor_transaction_id'] ?? null,
            'description' => $metadata['description'] ?? 'Debit',
            'metadata' => $metadata['metadata'] ?? null,
        ]);
    }

    public function refund(string $transactionReferenceId, ?Money $amount = null): Transaction
    {
        $originalTx = $this->transactionManager->getTransactionByReference($transactionReferenceId);

        if (! $originalTx) {
            throw new TransactionNotFoundException($transactionReferenceId);
        }

        if (! $amount) {
            $netAmount = $originalTx->amount - $originalTx->fee;
            $walletCurrency = $originalTx->wallet->currency;
            $currencyCode = $walletCurrency instanceof UnitEnum ? $walletCurrency->value : $walletCurrency;
            $amount = Money::make($netAmount, $currencyCode);
            $amount = $amount->absolute();
        }

        if ($amount->getAmount() <= 0) {
            throw new InvalidAmountException();
        }

        return $this->transactionManager->createTransaction([
            'wallet_id' => $originalTx->wallet_id,
            'type' => TransactionType::REFUND->value,
            'amount' => $amount->getAmount(),
            'fee' => -$originalTx->fee, // Refund fee as well
            'parent_transaction_id' => $originalTx->id,
            'reference_id' => 'REFUND_' . $transactionReferenceId . '_' . time(),
            'description' => "Refund of transaction {$transactionReferenceId}",
            'metadata' => ['refund_of' => $originalTx->reference_id],
        ]);
    }

    public function startRefund(string $transactionReferenceId): RefundBuilder
    {
        return new RefundBuilder($this, $transactionReferenceId);
    }

    public function transfer(int $fromWalletId, int $toWalletId, int|float|Money $amount, string|array $currency = 'USD', array $metadata = []): array
    {
        if (is_array($currency)) {
            $metadata = $currency;
            $currency = 'USD';
        }

        if (is_numeric($amount)) {
            $amount = Money::amount($amount, (string) $currency);
        }

        $fromWallet = $this->find($fromWalletId);
        $toWallet = $this->find($toWalletId);

        if (! $fromWallet) {
            throw new WalletNotFoundException("wallet_id:{$fromWalletId}");
        }
        if (! $toWallet) {
            throw new WalletNotFoundException("wallet_id:{$toWalletId}");
        }

        $this->assertCurrencyMatch($fromWallet, $amount);
        $this->assertCurrencyMatch($toWallet, $amount);

        if ($amount->getAmount() <= 0) {
            throw new InvalidAmountException();
        }

        $referenceId = $metadata['reference_id'] ?? $this->generateReferenceId();

        $executeTransfer = function () use ($fromWalletId, $toWalletId, $amount, $metadata, $referenceId) {
            // Debit from source wallet
            $debitTx = $this->transactionManager->createTransaction([
                'wallet_id' => $fromWalletId,
                'type' => TransactionType::TRANSFER_OUT->value,
                'amount' => $amount->getAmount(),
                'fee' => $metadata['fee'] ?? 0,
                'reference_id' => $referenceId . '_OUT',
                'description' => $metadata['description'] ?? 'Transfer out',
                'metadata' => $metadata['metadata'] ?? null,
            ]);

            // Credit to destination wallet
            $creditTx = $this->transactionManager->createTransaction([
                'wallet_id' => $toWalletId,
                'type' => TransactionType::TRANSFER_IN->value,
                'amount' => $amount->getAmount(),
                'fee' => 0,  // Fee charged on sender
                'reference_id' => $referenceId . '_IN',
                'parent_transaction_id' => $debitTx->id,
                'description' => $metadata['description'] ?? 'Transfer in',
                'metadata' => $metadata['metadata'] ?? null,
            ]);

            return ['debit' => $debitTx, 'credit' => $creditTx];
        };

        if (DB::connection()->inTransaction()) {
            return $executeTransfer();
        }

        return DB::transaction($executeTransfer);
    }

    public function getBalance(int $walletId): Money
    {
        return $this->balanceManager->getBalance($walletId);
    }

    public function reconcile(int $walletId): bool
    {
        return $this->balanceManager->reconcile($walletId);
    }

    private function assertCurrencyMatch(Wallet $wallet, Money $amount): void
    {
        $walletCurrency = $wallet->currency instanceof UnitEnum ? $wallet->currency->value : $wallet->currency;
        if ((string) $amount->getCurrency() !== $walletCurrency) {
            throw new CurrencyMismatchException(
                $wallet->currency,
                (string) $amount->getCurrency()
            );
        }
    }

    private function generateReferenceId(): string
    {
        return 'TXN_' . strtoupper(uniqid('', true)) . '_' . time();
    }

    public function analytics(): WalletAnalyticsService
    {
        return resolve(WalletAnalyticsService::class);
    }

    /**
     * Convert transaction attribute to Money object.
     */
    public function toMoney(Transaction $transaction, string $field = 'amount'): Money
    {
        $value = $transaction->{$field} ?? 0;
        $wallet = $transaction->wallet;
        $currencyCode = $wallet->currency instanceof UnitEnum ? $wallet->currency->value : $wallet->currency;

        return Money::make($value, MoneyCurrency::of((string) $currencyCode));
    }
}
