<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Channel Interface
 *
 * Defines the contract for OTP delivery channels (email, SMS, etc.)
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Contracts;

interface ChannelInterface
{
    /**
     * Send OTP code through this channel
     *
     * @param string $identifier The recipient identifier (email, phone, etc.)
     * @param string $code       The OTP code to send
     *
     * @return bool True if sent successfully
     */
    public function send(string $identifier, string $code): bool;
}
