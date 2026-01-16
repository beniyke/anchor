<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Has OTP Verification Trait
 *
 * Provides OTP verification functionality to any model.
 * Useful for User, Customer, or any entity requiring 2FA.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Traits;

use Verify\Verify;

trait HasOtpVerification
{
    /**
     * Send OTP code to this model's identifier
     */
    public function sendOtp(?string $channel = null, ?string $identifier = null, ?string $receiverName = null): bool
    {
        $channel = $channel ?? config('verify.default_channel', 'email');
        $identifier = $identifier ?? $this->getOtpIdentifier($channel);

        return Verify::send($identifier, $channel, $receiverName);
    }

    /**
     * Verify OTP code for this model
     */
    public function verifyOtp(string $code, ?string $identifier = null): bool
    {
        $identifier = $identifier ?? $this->getOtpIdentifier();

        return Verify::verify($identifier, $code);
    }

    /**
     * Resend OTP code to this model
     */
    public function resendOtp(?string $channel = null, ?string $identifier = null, ?string $receiverName = null): bool
    {
        $channel = $channel ?? config('verify.default_channel', 'email');
        $identifier = $identifier ?? $this->getOtpIdentifier($channel);

        return Verify::resend($identifier, $channel, $receiverName);
    }

    public function deleteOtp(?string $identifier = null): bool
    {
        $identifier = $identifier ?? $this->getOtpIdentifier();

        return Verify::delete($identifier);
    }

    /**
     * Check if model has a pending (valid, non-expired) OTP
     */
    public function hasPendingOtp(?string $identifier = null): bool
    {
        $identifier = $identifier ?? $this->getOtpIdentifier();

        return Verify::hasPending($identifier);
    }

    protected function getOtpIdentifier(?string $channel = null): string
    {
        if ($channel === 'sms' && property_exists($this, 'phone') && $this->phone) {
            return $this->phone;
        }

        if (property_exists($this, 'email') && $this->email) {
            return $this->email;
        }

        return (string) $this->id;
    }
}
