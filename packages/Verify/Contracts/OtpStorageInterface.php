<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Storage Interface
 *
 * Defines the contract for storing and retrieving OTP codes
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Contracts;

interface OtpStorageInterface
{
    /**
     * Store an OTP code for an identifier
     *
     * @param string $identifier The identifier (email, phone, user_id, etc.)
     * @param string $code       The OTP code (will be hashed)
     * @param string $channel    The channel used to send the code
     * @param int    $expiresIn  Expiration time in minutes
     *
     * @return bool True if stored successfully
     */
    public function store(string $identifier, string $code, string $channel, int $expiresIn): bool;

    /**
     * Verify an OTP code for an identifier
     *
     * @param string $identifier The identifier
     * @param string $code       The code to verify
     *
     * @return bool True if code is valid and not expired
     */
    public function verify(string $identifier, string $code): bool;

    /**
     * Get the current valid OTP for an identifier
     *
     * @param string $identifier The identifier
     *
     * @return array|null OTP data or null if not found/expired
     */
    public function get(string $identifier): ?array;

    /**
     * Mark OTP as verified
     *
     * @param string $identifier The identifier
     *
     * @return bool True if marked successfully
     */
    public function markAsVerified(string $identifier): bool;

    /**
     * Delete OTP for an identifier
     *
     * @param string $identifier The identifier
     *
     * @return bool True if deleted successfully
     */
    public function delete(string $identifier): bool;

    /**
     * Check if identifier has a pending (valid, non-expired) OTP
     *
     * @param string $identifier The identifier
     *
     * @return bool True if has pending OTP
     */
    public function hasPending(string $identifier): bool;

    /**
     * Clean up expired OTP codes
     *
     * @return int Number of codes cleaned up
     */
    public function cleanup(): int;
}
