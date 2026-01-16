<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Storage Service
 *
 * Handles database persistence of OTP codes
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Verify\Contracts\OtpStorageInterface;
use Verify\Exceptions\OtpExpiredException;
use Verify\Exceptions\OtpInvalidException;
use Verify\Exceptions\OtpNotFoundException;

class OtpStorageService implements OtpStorageInterface
{
    private const TABLE_NAME = 'verify_otp_code';

    /**
     * Store an OTP code for an identifier
     */
    public function store(string $identifier, string $code, string $channel, int $expiresIn): bool
    {
        $hashedCode = password_hash($code, PASSWORD_DEFAULT);
        $expiresAt = DateTimeHelper::now()->addMinutes($expiresIn)->toDateTimeString();

        return DB::transaction(function () use ($identifier, $hashedCode, $channel, $expiresAt) {
            DB::table(self::TABLE_NAME)
                ->where('identifier', $identifier)
                ->delete();

            return DB::table(self::TABLE_NAME)->insert([
                'identifier' => $identifier,
                'refid' => Str::random('secure'),
                'code' => $hashedCode,
                'channel' => $channel,
                'expires_at' => $expiresAt,
                'verified_at' => null,
                'created_at' => DateTimeHelper::now()->toDateTimeString(),
                'updated_at' => DateTimeHelper::now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * Verify an OTP code for an identifier
     */
    public function verify(string $identifier, string $code): bool
    {
        $otp = $this->get($identifier);

        if (! $otp) {
            throw new OtpNotFoundException($identifier);
        }

        $expiresAt = DateTimeHelper::parse($otp['expires_at']);

        if (DateTimeHelper::now()->greaterThan($expiresAt)) {
            throw new OtpExpiredException($identifier);
        }

        if (! password_verify($code, $otp['code'])) {
            throw new OtpInvalidException($identifier);
        }

        return true;
    }

    /**
     * Check if identifier has a pending (valid, non-expired) OTP
     */
    public function hasPending(string $identifier): bool
    {
        $record = DB::table(self::TABLE_NAME)
            ->where('identifier', $identifier)
            ->first();

        if (! $record) {
            return false;
        }

        $expiresAt = DateTimeHelper::parse($record->expires_at);

        if (DateTimeHelper::now()->greaterThan($expiresAt)) {
            return false;
        }

        if ($record->verified_at !== null) {
            return false;
        }

        return true;
    }

    /**
     * Get the current valid OTP for an identifier
     */
    public function get(string $identifier): ?array
    {
        $otp = DB::table(self::TABLE_NAME)
            ->where('identifier', $identifier)
            ->whereNull('verified_at')
            ->first();

        if (! $otp) {
            return null;
        }

        return (array) $otp;
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified(string $identifier): bool
    {
        $result = DB::table(self::TABLE_NAME)
            ->where('identifier', $identifier)
            ->whereNull('verified_at')
            ->update([
                'verified_at' => DateTimeHelper::now()->toDateTimeString(),
                'updated_at' => DateTimeHelper::now()->toDateTimeString(),
            ]);

        return $result > 0;
    }

    /**
     * Delete OTP for an identifier
     */
    public function delete(string $identifier): bool
    {
        $result = DB::table(self::TABLE_NAME)
            ->where('identifier', $identifier)
            ->delete();

        return $result > 0;
    }

    /**
     * Clean up expired OTP codes
     */
    public function cleanup(): int
    {
        $now = DateTimeHelper::now()->toDateTimeString();

        return DB::table(self::TABLE_NAME)
            ->whereLessThan('expires_at', $now)
            ->delete();
    }
}
