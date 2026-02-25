<?php

declare(strict_types=1);

namespace Academy\DataObjects;

class CertificateData
{
    public function __construct(
        public string $certificateNumber,
        public string $verificationCode,
        public string $issuedAt,
        public ?string $filePath = null
    ) {
    }
}
