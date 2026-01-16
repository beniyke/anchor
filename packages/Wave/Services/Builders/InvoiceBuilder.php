<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Invoice Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services\Builders;

use Exception;
use Helpers\DateTimeHelper;
use Money\Money;
use Wave\Enums\InvoiceStatus;
use Wave\Models\Invoice;
use Wave\Services\InvoiceManagerService;

class InvoiceBuilder
{
    private string|int|null $ownerId = null;

    private string|null $ownerType = null;

    private array $data = [];

    public function __construct(
        private readonly InvoiceManagerService $manager
    ) {
    }

    public function for(object|string|int $owner, ?string $type = null): self
    {
        if (is_object($owner)) {
            $this->ownerId = $owner->id ?? throw new Exception("Owner object must have an ID");
            $this->ownerType = $type ?? (method_exists($owner, 'getMorphClass') ? $owner->getMorphClass() : get_class($owner));
        } else {
            $this->ownerId = $owner;
            $this->ownerType = $type;
        }

        return $this;
    }

    public function amount(int|float $amount): self
    {
        $currency = $this->data['currency'] ?? 'USD';

        if (is_float($amount)) {
            $money = Money::amount($amount, $currency);
        } else {
            $money = Money::make($amount, $currency);
        }

        $this->data['amount'] = (int) $money->getAmount();

        return $this;
    }

    public function currency(string $currency): self
    {
        $this->data['currency'] = $currency;

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function discount(string $code): self
    {
        $this->data['coupon_code'] = $code;

        return $this;
    }

    public function dueInDays(int $days): self
    {
        $this->data['due_at'] = DateTimeHelper::now()->addDays($days);

        return $this;
    }

    public function due($date): self
    {
        $this->data['due_at'] = $date;

        return $this;
    }

    public function dueNow(): self
    {
        $this->data['due_at'] = DateTimeHelper::now();

        return $this;
    }

    public function paid(): self
    {
        $this->data['status'] = InvoiceStatus::PAID->value;
        $this->data['paid_at'] = DateTimeHelper::now();

        return $this;
    }

    public function create(): Invoice
    {
        if (!$this->ownerId || !$this->ownerType) {
            throw new Exception("Invoice owner not specified.");
        }

        $payload = array_merge($this->data, [
            'owner_id' => $this->ownerId,
            'owner_type' => $this->ownerType,
        ]);

        return $this->manager->create($payload);
    }
}
