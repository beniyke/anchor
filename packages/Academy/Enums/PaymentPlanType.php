<?php

declare(strict_types=1);

namespace Academy\Enums;

enum PaymentPlanType: string
{
    case FULL = 'full';
    case INSTALMENT = 'instalment';
    case FREE = 'free';
    case SUBSCRIPTION = 'subscription';

    public function label(): string
    {
        return match ($this) {
            self::FULL => 'Full Payment',
            self::INSTALMENT => 'Instalment Plan',
            self::FREE => 'Free',
            self::SUBSCRIPTION => 'Subscription',
        };
    }
}
