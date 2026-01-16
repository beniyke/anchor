<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for feature flag operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout;

use App\Models\User;
use Rollout\Models\Feature;
use Rollout\Services\Builders\FeatureBuilder;
use Rollout\Services\RolloutManagerService;

class Rollout
{
    public static function isEnabled(string $feature, ?User $user = null, array $context = []): bool
    {
        return resolve(RolloutManagerService::class)->isEnabled($feature, $user, $context);
    }

    public static function isActive(string $feature, array $context = []): bool
    {
        return static::isEnabled($feature, null, $context);
    }

    public static function isDisabled(string $feature, ?User $user = null): bool
    {
        return !static::isEnabled($feature, $user);
    }

    /**
     * Create a new feature builder.
     */
    public static function feature(): FeatureBuilder
    {
        return resolve(RolloutManagerService::class)->feature();
    }

    public static function enable(string $feature): Feature
    {
        return resolve(RolloutManagerService::class)->enable($feature);
    }

    public static function disable(string $feature): Feature
    {
        return resolve(RolloutManagerService::class)->disable($feature);
    }

    /**
     * Set percentage rollout for a feature.
     */
    public static function setPercentage(string $feature, int $percentage): Feature
    {
        return resolve(RolloutManagerService::class)->setPercentage($feature, $percentage);
    }

    public static function all(): array
    {
        return resolve(RolloutManagerService::class)->all();
    }

    public static function get(string $feature): ?Feature
    {
        return resolve(RolloutManagerService::class)->find($feature);
    }

    /**
     * Clear feature cache.
     */
    public static function clearCache(): void
    {
        resolve(RolloutManagerService::class)->clearCache();
    }

    /**
     * Forward static calls to RolloutManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(RolloutManagerService::class)->$method(...$arguments);
    }
}
