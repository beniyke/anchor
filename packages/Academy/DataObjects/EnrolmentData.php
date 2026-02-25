<?php

declare(strict_types=1);

namespace Academy\DataObjects;

class EnrolmentData
{
    public function __construct(
        public int $userId,
        public int $programId,
        public ?int $paymentPlanId = null,
        public ?string $couponCode = null,
        public array $metadata = []
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['program_id'],
            $data['payment_plan_id'] ?? null,
            $data['coupon_code'] ?? null,
            $data['metadata'] ?? []
        );
    }
}
