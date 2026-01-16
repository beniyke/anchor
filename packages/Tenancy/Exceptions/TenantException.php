<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown for tenant-related issues.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Exceptions;

use RuntimeException;
use Throwable;

class TenantException extends RuntimeException
{
    public static function notFound(string $subdomain): self
    {
        return new self("Tenant not found for subdomain: {$subdomain}");
    }

    public static function suspended(string $subdomain): self
    {
        return new self("Tenant is suspended: {$subdomain}");
    }

    public static function expired(string $subdomain): self
    {
        return new self("Tenant subscription has expired: {$subdomain}");
    }

    public static function databaseConnectionFailed(string $subdomain, Throwable $previous = null): self
    {
        return new self(
            "Failed to connect to tenant database: {$subdomain}",
            0,
            $previous
        );
    }

    public static function invalidSubdomain(string $subdomain): self
    {
        return new self("Invalid subdomain format: {$subdomain}");
    }

    public static function alreadyExists(string $subdomain): self
    {
        return new self("Tenant already exists: {$subdomain}");
    }
}
