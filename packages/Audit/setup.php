<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Audit Package Setup
 *
 * Comprehensive activity logging and audit trail for the Anchor Framework.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'providers' => [
        Audit\Providers\AuditServiceProvider::class,
    ],
    'middleware' => [],
];
