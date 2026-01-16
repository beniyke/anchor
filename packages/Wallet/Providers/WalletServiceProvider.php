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

use Core\Event;
use Core\Services\ServiceProvider;
use Helpers\String\Str;
use Pay\Events\PaymentSuccessfulEvent;
use Wallet\Listeners\WalletFundingListener;
use Wallet\Models\Transaction;
use Wallet\Models\Wallet;
use Wallet\Services\BalanceManagerService;
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
}
