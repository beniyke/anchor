<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for referral operations.
 *
 * @method static Referral|null complete(string $code, int $refereeId) Alias for track()
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer;

use Refer\Models\Referral;
use Refer\Models\ReferralCode;
use Refer\Services\ReferAnalyticsService;
use Refer\Services\ReferManagerService;
use Refer\Services\ReferRateLimiterService;

class Refer
{
    /**
     * Start a fluent referral for a user.
     */
    public static function for(int $userId): ReferralCode
    {
        return resolve(ReferManagerService::class)->generateCode($userId);
    }

    /**
     * Generate a referral code for a user.
     */
    public static function generateCode(int $userId, ?string $customCode = null): ReferralCode
    {
        return resolve(ReferManagerService::class)->generateCode($userId, $customCode);
    }

    public static function getCode(int $userId): ?ReferralCode
    {
        return resolve(ReferManagerService::class)->getCode($userId);
    }

    public static function track(string $code, int $refereeId): ?Referral
    {
        return resolve(ReferManagerService::class)->track($code, $refereeId);
    }

    public static function reward(Referral $referral): bool
    {
        return resolve(ReferManagerService::class)->reward($referral);
    }

    /**
     * Get user's referrals.
     */
    public static function getReferrals(int $userId): array
    {
        return resolve(ReferManagerService::class)->getReferrals($userId);
    }

    public static function getStats(int $userId): array
    {
        return resolve(ReferManagerService::class)->getStats($userId);
    }

    public static function analytics(): ReferAnalyticsService
    {
        return resolve(ReferAnalyticsService::class);
    }

    public static function rateLimiter(): ReferRateLimiterService
    {
        return resolve(ReferRateLimiterService::class);
    }

    /**
     * Forward static calls to ReferManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(ReferManagerService::class)->$method(...$arguments);
    }
}
