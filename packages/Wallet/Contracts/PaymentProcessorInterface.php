<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Payment Processor Interface
 *
 * Contract for payment gateway adapters (Stripe, PayPal, etc.)
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Contracts;

use Money\Money;

interface PaymentProcessorInterface
{
    /**
     * Charge a payment
     *
     * @param Money $amount   Amount to charge
     * @param array $metadata Additional data (customer_id, description, etc.)
     *
     * @return array ['success' => bool, 'transaction_id' => string, 'fee' => Money, 'metadata' => array]
     */
    public function charge(Money $amount, array $metadata = []): array;

    /**
     * Process a refund
     *
     * @param string $transactionId Processor transaction ID
     * @param Money  $amount        Amount to refund
     *
     * @return array ['success' => bool, 'refund_id' => string, 'metadata' => array]
     */
    public function refund(string $transactionId, Money $amount): array;

    /**
     * Verify a transaction with the processor
     *
     * @param string $transactionId Processor transaction ID
     *
     * @return bool True if transaction is valid
     */
    public function verify(string $transactionId): bool;

    public function getProcessorName(): string;
}
