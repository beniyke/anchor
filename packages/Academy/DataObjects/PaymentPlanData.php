<?php

declare(strict_types=1);

namespace Academy\DataObjects;

class PaymentPlanData
{
    public function __construct(
        public string $name,
        public string $type,
        public int $totalAmount,
        public int $instalmentCount = 1,
        public int $intervalDays = 30,
        public ?int $depositAmount = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['type'],
            $data['total_amount'],
            $data['instalment_count'] ?? 1,
            $data['interval_days'] ?? 30,
            $data['deposit_amount'] ?? null
        );
    }
}
