<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Refund Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services\Builders;

use InvalidArgumentException;
use Money\Money;
use Wallet\Models\Transaction;
use Wallet\Services\WalletManagerService;

class RefundBuilder
{
    private ?Money $amount = null;

    private array $metadata = [];

    public function __construct(
        private readonly WalletManagerService $manager,
        private readonly string $transactionReferenceId
    ) {
    }

    public function amount(int|float|Money $amount): self
    {
        // Currency is unknown here until execute, but we can assume null implies
        // it will be validated against original transaction in manager,
        // OR we need to fetch transaction first.
        // Manager::refund checks currency against original tx?
        // Manager::refund takes Money. Money needs currency.
        // We should fetch the transaction to get currency if int is passed.
        // But optimization: Let's pass null currency to manager and let it handle?
        // WalletManagerService::refund needs Money.
        // So we must fetch transaction here.

        $tx = $this->manager->getTransactionByReference($this->transactionReferenceId);
        if (!$tx) {
            throw new InvalidArgumentException("Transaction not found: {$this->transactionReferenceId}");
        }

        $currency = $tx->currency; // Assuming transaction has currency or we get it from wallet
        // Wait, Transaction model has 'wallet_id'. Wallet has currency.
        // Transaction doesn't usually store currency directly if it's single currency wallet.
        // But Money::make requires currency.
        // Let's get wallet from transaction.
        $wallet = $this->manager->find($tx->wallet_id);

        $this->amount = $amount instanceof Money ? $amount : Money::amount($amount, $wallet->currency);

        return $this;
    }

    public function execute(): Transaction
    {
        return $this->manager->refund(
            $this->transactionReferenceId,
            $this->amount
        );
    }
}
