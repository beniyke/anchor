<?php

declare(strict_types=1);

namespace Academy\Enums;

enum CertificateStatus: string
{
    case ISSUED = 'issued';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::ISSUED => 'Issued',
            self::REVOKED => 'Revoked',
            self::EXPIRED => 'Expired',
        };
    }
}
