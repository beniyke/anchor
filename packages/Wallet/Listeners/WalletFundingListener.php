<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Funding Listener
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Listeners;

use Money\Money;
use Pay\Enums\Currency;
use Pay\Events\PaymentSuccessfulEvent;
use Wallet\Services\WalletManagerService;

class WalletFundingListener
{
    public function __construct(
        protected WalletManagerService $walletManager
    ) {
    }

    public function handle(PaymentSuccessfulEvent $event): void
    {
        $transaction = $event->transaction;
        $metadata = $transaction->metadata ?? [];

        if (! isset($metadata['wallet_id']) || ($metadata['intention'] ?? '') !== 'fund') {
            return;
        }

        $walletId = (int) $metadata['wallet_id'];
        $currency = $transaction->currency instanceof Currency
            ? $transaction->currency->value
            : (string) $transaction->currency;
        $amount = Money::make($transaction->amount, $currency);

        $this->walletManager->credit(
            $walletId,
            $amount,
            [
                'description' => 'Wallet Funding via ' . ucfirst($transaction->driver),
                'payment_processor' => $transaction->driver,
                'processor_transaction_id' => $transaction->reference,
                'reference_id' => 'FUND_' . $transaction->reference,
                'metadata' => [
                    'pay_reference' => $transaction->reference,
                    'source' => 'pay_webhook'
                ]
            ]
        );
    }
}
