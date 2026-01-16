<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Ally Facade provides a static interface for reseller operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally;

use Ally\Models\Reseller;
use Ally\Services\AllyManagerService;
use Ally\Services\AnalyticsManagerService;
use Ally\Services\Builders\AllyBuilder;

class Ally
{
    /**
     * Start a fluent reseller registration builder.
     */
    public static function make(): AllyBuilder
    {
        return resolve(AllyManagerService::class)->make();
    }

    public static function analytics(): AnalyticsManagerService
    {
        return resolve(AnalyticsManagerService::class);
    }

    public static function findByUser(int|string $userId): ?Reseller
    {
        return resolve(AllyManagerService::class)->findByUser($userId);
    }

    public static function findByRefid(string $refid): ?Reseller
    {
        return resolve(AllyManagerService::class)->findByRefid($refid);
    }

    public static function addCredits(Reseller|int|string $reseller, int $amount): void
    {
        resolve(AllyManagerService::class)->addCredits($reseller, $amount);
    }

    /**
     * Provision a service by deducting credits.
     */
    public static function provision(Reseller|int|string $reseller, int $cost, string $action = 'Service Provisioning'): bool
    {
        return resolve(AllyManagerService::class)->provision($reseller, $cost, $action);
    }

    /**
     * Forward static calls to AllyManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(AllyManagerService::class)->$method(...$arguments);
    }
}
