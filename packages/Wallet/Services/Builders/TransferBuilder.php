<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transfer Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services\Builders;

use InvalidArgumentException;
use Money\Money;
use Wallet\Models\Wallet;
use Wallet\Services\WalletManagerService;

class TransferBuilder
{
    private ?Money $amount = null;

    private ?Wallet $toWallet = null;

    private array $metadata = [];

    private ?string $referenceId = null;

    public function __construct(
        private readonly WalletManagerService $manager,
        private readonly Wallet $fromWallet
    ) {
    }

    public function amount(int|float|Money $amount): self
    {
        $this->amount = $amount instanceof Money ? $amount : Money::amount($amount, $this->fromWallet->currency);

        return $this;
    }

    public function to(mixed $recipient): self
    {
        if ($recipient instanceof Wallet) {
            $this->toWallet = $recipient;
        } elseif (is_object($recipient) && method_exists($recipient, 'getOrCreateWallet')) {
            // Get wallet with matching currency to the 'from' wallet
            $this->toWallet = $recipient->getOrCreateWallet($this->fromWallet->currency);
        } else {
            throw new InvalidArgumentException("Recipient must be a Wallet model or use HasWallet trait.");
        }

        return $this;
    }

    public function description(string $description): self
    {
        $this->metadata['description'] = $description;

        return $this;
    }

    public function meta(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }

    public function execute(): array
    {
        if (!$this->amount || !$this->toWallet) {
            throw new InvalidArgumentException("Amount and destination wallet are required for transfer.");
        }

        return $this->manager->transfer(
            $this->fromWallet->id,
            $this->toWallet->id,
            $this->amount,
            $this->metadata
        );
    }
}
