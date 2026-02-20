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

use App\Models\User;
use Core\Services\ServiceProvider;
use Verify\Contracts\OtpGeneratorInterface;
use Verify\Contracts\OtpStorageInterface;
use Verify\Services\OtpGeneratorService;
use Verify\Services\OtpStorageService;
use Verify\Services\RateLimiterService;
use Verify\Services\VerifyManagerService;
use Verify\Verify;

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

    public function boot(): void
    {
        $this->registerUserMacros();
    }

    protected function registerUserMacros(): void
    {
        User::macro('sendOtp', function (?string $channel = null, ?string $identifier = null, ?string $receiverName = null) {
            $channel = $channel ?? config('verify.default_channel', 'email');
            $identifier = $identifier ?? $this->getOtpIdentifier($channel);

            return Verify::send($identifier, $channel, $receiverName);
        });

        User::macro('verifyOtp', function (string $code, ?string $identifier = null) {
            $identifier = $identifier ?? $this->getOtpIdentifier();

            return Verify::verify($identifier, $code);
        });

        User::macro('resendOtp', function (?string $channel = null, ?string $identifier = null, ?string $receiverName = null) {
            $channel = $channel ?? config('verify.default_channel', 'email');
            $identifier = $identifier ?? $this->getOtpIdentifier($channel);

            return Verify::resend($identifier, $channel, $receiverName);
        });

        User::macro('deleteOtp', function (?string $identifier = null) {
            $identifier = $identifier ?? $this->getOtpIdentifier();

            return Verify::delete($identifier);
        });

        User::macro('hasPendingOtp', function (?string $identifier = null) {
            $identifier = $identifier ?? $this->getOtpIdentifier();

            return Verify::hasPending($identifier);
        });

        User::macro('getOtpIdentifier', function (?string $channel = null) {
            if ($channel === 'sms' && !empty($this->phone)) {
                return $this->phone;
            }

            if (!empty($this->email)) {
                return $this->email;
            }

            return (string) $this->id;
        });
    }
}
