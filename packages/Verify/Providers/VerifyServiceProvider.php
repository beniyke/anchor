<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Verify Service Provider
 *
 * Registers OTP verification services
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Providers;

use Core\Services\ServiceProvider;
use Verify\Contracts\OtpGeneratorInterface;
use Verify\Contracts\OtpStorageInterface;
use Verify\Services\OtpGeneratorService;
use Verify\Services\OtpStorageService;
use Verify\Services\RateLimiterService;
use Verify\Services\VerifyManagerService;

class VerifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->bind(OtpGeneratorInterface::class, OtpGeneratorService::class);
        $this->container->bind(OtpStorageInterface::class, OtpStorageService::class);

        $this->container->singleton(OtpGeneratorService::class);
        $this->container->singleton(OtpStorageService::class);
        $this->container->singleton(RateLimiterService::class);
        $this->container->singleton(VerifyManagerService::class);
    }
}
