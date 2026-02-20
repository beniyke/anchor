<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Service Provider
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Providers;

use App\Models\User;
use Core\Event;
use Core\Services\ServiceProvider;
use Database\Collections\ModelCollection;
use Helpers\String\Str;
use Money\Money;
use Pay\Events\PaymentSuccessfulEvent;
use Wallet\Listeners\WalletFundingListener;
use Wallet\Models\Transaction;
use Wallet\Models\Wallet;
use Wallet\Services\BalanceManagerService;
use Wallet\Services\Builders\TransferBuilder;
use Wallet\Services\FeeCalculatorService;
use Wallet\Services\TransactionManagerService;
use Wallet\Services\WalletManagerService;

class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(FeeCalculatorService::class);
        $this->container->singleton(BalanceManagerService::class);
        $this->container->singleton(TransactionManagerService::class);
        $this->container->singleton(WalletManagerService::class);
    }

    public function boot(): void
    {
        $this->registerUserMacros();

        Event::listen(PaymentSuccessfulEvent::class, WalletFundingListener::class);

        Wallet::creating(function ($wallet) {
            if (empty($wallet->refid)) {
                $wallet->refid = Str::random('secure');
            }
        });

        Transaction::creating(function ($transaction) {
            if (empty($transaction->refid)) {
                $transaction->refid = Str::random('secure');
            }
        });
    }

    protected function registerUserMacros(): void
    {
        $container = $this->container;

        User::macro('wallet', function () {
            return $this->morphOne(Wallet::class, 'owner', 'owner_type', 'owner_id');
        });

        User::macro('createWallet', function (string $currency = 'USD') use ($container) {
            return $container->get(WalletManagerService::class)->create(
                $this->id,
                static::class,
                $currency
            );
        });

        User::macro('getOrCreateWallet', function (string $currency = 'USD') {
            $wallet = $this->wallet()->where('currency', $currency)->first();

            if (!$wallet) {
                $wallet = $this->createWallet($currency);
            }

            return $wallet;
        });

        User::macro('credit', function (Money $amount, array $metadata = []) use ($container) {
            $wallet = $this->getOrCreateWallet((string) $amount->getCurrency());

            return $container->get(WalletManagerService::class)->credit($wallet->id, $amount, $metadata);
        });

        User::macro('debit', function (Money $amount, array $metadata = []) use ($container) {
            $wallet = $this->getOrCreateWallet((string) $amount->getCurrency());

            return $container->get(WalletManagerService::class)->debit($wallet->id, $amount, $metadata);
        });

        User::macro('getBalance', function (string $currency = 'USD') use ($container) {
            $wallet = $this->wallet()->where('currency', $currency)->first();

            if (!$wallet) {
                return Money::make(0, $currency);
            }

            return $container->get(WalletManagerService::class)->getBalance($wallet->id);
        });

        User::macro('transactions', function (string $currency = 'USD') {
            $wallet = $this->wallet()->where('currency', $currency)->first();

            if (!$wallet) {
                return new ModelCollection([]);
            }

            return $wallet->transactions()->get();
        });

        User::macro('hasSufficientFunds', function (Money $amount) {
            $balance = $this->getBalance((string) $amount->getCurrency());

            return $balance->greaterThanOrEqual($amount);
        });

        User::macro('transaction', function (string $currency = 'USD') use ($container) {
            $wallet = $this->getOrCreateWallet($currency);

            return $container->get(WalletManagerService::class)->transaction($wallet->id);
        });

        User::macro('canAfford', function (int|float|Money $amount, string $currency = 'USD') {
            if (is_numeric($amount)) {
                $amount = Money::amount($amount, $currency);
            }

            return $this->getBalance((string) $amount->getCurrency())->greaterThanOrEqual($amount);
        });

        User::macro('transfer', function (int|float|Money $amount, string $currency = 'USD') use ($container) {
            if (is_numeric($amount)) {
                $amount = Money::amount($amount, $currency);
            }

            $wallet = $this->getOrCreateWallet((string) $amount->getCurrency());

            return (new TransferBuilder(
                $container->get(WalletManagerService::class),
                $wallet
            ))->amount($amount);
        });
    }
}
