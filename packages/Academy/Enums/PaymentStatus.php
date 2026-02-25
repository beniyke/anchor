<?php

declare(strict_types=1);

namespace Academy\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case OVERDUE = 'overdue';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::PARTIAL => 'Partially Paid',
            self::OVERDUE => 'Overdue',
            self::REFUNDED => 'Refunded',
            self::FAILED => 'Failed',
        };
    }
}
