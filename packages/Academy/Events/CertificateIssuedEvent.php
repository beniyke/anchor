<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyCertificate;

class CertificateIssuedEvent
{
    public function __construct(public AcademyCertificate $certificate)
    {
    }
}
