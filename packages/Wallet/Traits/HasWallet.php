<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Has Wallet Trait
 *
 * Add to any model (User, Business, etc.) to give it wallet capabilities
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Traits;

use Database\Collections\ModelCollection;
use Database\Relations\MorphOne;
use Money\Money;
use Wallet\Models\Transaction;
use Wallet\Models\Wallet;
use Wallet\Services\Builders\TransactionBuilder;
use Wallet\Services\Builders\TransferBuilder;
use Wallet\Services\WalletManagerService;

trait HasWallet
{
    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'owner', 'owner_type', 'owner_id');
    }

    public function createWallet(string $currency = 'USD'): Wallet
    {
        return resolve(WalletManagerService::class)->create(
            $this->id,
            static::class,
            $currency
        );
    }

    public function getOrCreateWallet(string $currency = 'USD'): Wallet
    {
        $wallet = $this->wallet()->where('currency', $currency)->first();

        if (! $wallet) {
            $wallet = $this->createWallet($currency);
        }

        return $wallet;
    }

    public function credit(Money $amount, array $metadata = []): Transaction
    {
        $wallet = $this->getOrCreateWallet((string) $amount->getCurrency());

        return resolve(WalletManagerService::class)->credit($wallet->id, $amount, $metadata);
    }

    public function debit(Money $amount, array $metadata = []): Transaction
    {
        $wallet = $this->getOrCreateWallet((string) $amount->getCurrency());

        return resolve(WalletManagerService::class)->debit($wallet->id, $amount, $metadata);
    }

    public function getBalance(string $currency = 'USD'): Money
    {
        $wallet = $this->wallet()->where('currency', $currency)->first();

        if (! $wallet) {
            return Money::make(0, $currency);
        }

        return resolve(WalletManagerService::class)->getBalance($wallet->id);
    }

    public function transactions(string $currency = 'USD'): ModelCollection
    {
        $wallet = $this->wallet()->where('currency', $currency)->first();

        if (! $wallet) {
            return new ModelCollection([]);
        }

        return $wallet->transactions()->get();
    }

    public function hasSufficientFunds(Money $amount): bool
    {
        $balance = $this->getBalance((string) $amount->getCurrency());

        return $balance->greaterThanOrEqual($amount);
    }

    public function transaction(string $currency = 'USD'): TransactionBuilder
    {
        $wallet = $this->getOrCreateWallet($currency);

        return resolve(WalletManagerService::class)->transaction($wallet->id);
    }

    public function canAfford(int|float|Money $amount, string $currency = 'USD'): bool
    {
        if (is_numeric($amount)) {
            $amount = Money::amount($amount, $currency);
        }

        return $this->getBalance((string) $amount->getCurrency())->greaterThanOrEqual($amount);
    }

    public function transfer(int|float|Money $amount, string $currency = 'USD'): TransferBuilder
    {
        if (is_numeric($amount)) {
            $amount = Money::amount($amount, $currency);
        }

        $wallet = $this->getOrCreateWallet((string) $amount->getCurrency());

        return (new TransferBuilder(
            resolve(WalletManagerService::class),
            $wallet
        ))->amount($amount);
    }
}
