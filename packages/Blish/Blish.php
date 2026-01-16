<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Blish.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish;

use Blish\Models\Subscriber;
use Blish\Services\BlishAnalyticsService;
use Blish\Services\BlishManagerService;
use Blish\Services\Builders\CampaignBuilder;
use Blish\Services\Builders\SubscriberBuilder;

/**
 * @method static SubscriberBuilder     subscriber()
 * @method static CampaignBuilder       campaign()
 * @method static Subscriber            subscribe(string $email, array $data = [])
 * @method static void                  unsubscribe(string $email)
 * @method static Subscriber|null       find(string $email)
 * @method static BlishAnalyticsService analytics()
 */
class Blish
{
    public static function subscriber(): SubscriberBuilder
    {
        return resolve(BlishManagerService::class)->subscriber();
    }

    public static function campaign(): CampaignBuilder
    {
        return resolve(BlishManagerService::class)->campaign();
    }

    public static function subscribe(string $email, array $data = []): Subscriber
    {
        return resolve(BlishManagerService::class)->subscribe($email, $data);
    }

    public static function unsubscribe(string $email): void
    {
        resolve(BlishManagerService::class)->unsubscribe($email);
    }

    public static function find(string $email): ?Subscriber
    {
        return resolve(BlishManagerService::class)->find($email);
    }

    public static function analytics(): BlishAnalyticsService
    {
        return resolve(BlishAnalyticsService::class);
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(BlishManagerService::class)->$method(...$arguments);
    }
}
