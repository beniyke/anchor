<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services\Builders;

use Money\Money;
use Wallet\Enums\Currency;
use Wallet\Models\Transaction;
use Wallet\Models\Wallet;
use Wallet\Services\WalletManagerService;

class TransactionBuilder
{
    private ?Money $amount = null;

    private string $type = 'credit'; // 'credit' or 'debit'

    private ?string $description = null;

    private array $metadata = [];

    private ?string $referenceId = null;

    private ?string $paymentProcessor = null;

    private ?string $processorTransactionId = null;

    private bool $calculateFee = false;

    private ?int $fee = null;

    private ?float $feePercent = null;

    public function __construct(
        private readonly WalletManagerService $manager,
        private readonly Wallet $wallet
    ) {
    }

    public function credit(int|float|Money $amount, string|Currency|null $currency = null): self
    {
        $this->type = 'credit';
        $currencyCode = $currency instanceof Currency ? $currency->value : $currency;
        $this->amount = $amount instanceof Money ? $amount : Money::amount($amount, $currencyCode ?? ($this->wallet->currency instanceof Currency ? $this->wallet->currency->value : $this->wallet->currency));

        return $this;
    }

    public function amount(int|float|Money $amount, string|Currency|null $currency = null): self
    {
        $currencyCode = $currency instanceof Currency ? $currency->value : $currency;
        $this->amount = $amount instanceof Money ? $amount : Money::amount($amount, $currencyCode ?? ($this->wallet->currency instanceof Currency ? $this->wallet->currency->value : $this->wallet->currency));

        return $this;
    }

    public function debit(int|float|Money $amount, string|Currency|null $currency = null): self
    {
        $this->type = 'debit';
        $currencyCode = $currency instanceof Currency ? $currency->value : $currency;
        $this->amount = $amount instanceof Money ? $amount : Money::amount($amount, $currencyCode ?? ($this->wallet->currency instanceof Currency ? $this->wallet->currency->value : $this->wallet->currency));

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function meta(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }

    public function with(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    public function reference(string $referenceId): self
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    public function processor(string $processor, ?string $transactionId = null): self
    {
        $this->paymentProcessor = $processor;
        $this->processorTransactionId = $transactionId;

        return $this;
    }

    /**
     * Set a fixed fee amount (in major currency unit, e.g., dollars)
     */
    public function fee(int|float $fee): self
    {
        $currency = $this->wallet->currency instanceof Currency ? $this->wallet->currency->value : $this->wallet->currency;
        $this->fee = Money::amount($fee, $currency)->getAmount();

        return $this;
    }

    /**
     * Set fee as a percentage of the transaction amount
     *
     * @param float|int $percent Percentage value (e.g., 2.5 for 2.5%)
     */
    public function feePercent(int|float $percent): self
    {
        $this->feePercent = (float) $percent;

        return $this;
    }

    public function calculateFee(): self
    {
        $this->calculateFee = true;

        return $this;
    }

    public function execute(): Transaction
    {
        $method = $this->type;

        // Calculate percentage fee if set
        $fee = $this->fee;
        if ($this->feePercent !== null && $this->amount !== null) {
            $fee = (int) round($this->amount->getAmount() * ($this->feePercent / 100));
        }

        $meta = [
            'description' => $this->description,
            'metadata' => $this->metadata,
            'reference_id' => $this->referenceId,
            'payment_processor' => $this->paymentProcessor,
            'processor_transaction_id' => $this->processorTransactionId,
            'calculate_fee' => $this->calculateFee,
            'fee' => $fee,
        ];

        // Filter nulls
        $meta = array_filter($meta, fn ($value) => !is_null($value));

        return $this->manager->$method($this->wallet->id, $this->amount, $meta);
    }
}
