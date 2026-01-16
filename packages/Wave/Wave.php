<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wave Facade for subscription billing.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave;

use Wallet\Services\WalletManagerService;
use Wave\Models\Plan;
use Wave\Models\Product;
use Wave\Models\Subscription;
use Wave\Services\AnalyticsManagerService;
use Wave\Services\Builders\SubscriptionBuilder;
use Wave\Services\CouponManagerService;
use Wave\Services\InvoiceManagerService;
use Wave\Services\PlanManagerService;
use Wave\Services\SubscriptionManagerService;
use Wave\Services\WaveManagerService;

/**
 * @method static Subscription               subscribe(string|int $ownerId, string $ownerType, string|int $planId, array $options = [])
 * @method static Subscription|null          findSubscription(string|int $id)
 * @method static Plan|null                  findPlan(string|int $id)
 * @method static Product|null               findProduct(string|int $id)
 * @method static Product                    createProduct(array $data)
 * @method static SubscriptionManagerService subscriptions()
 * @method static InvoiceManagerService      invoices()
 * @method static PlanManagerService         plan()
 * @method static CouponManagerService       coupons()
 * @method static WalletManagerService       wallet()
 * @method static SubscriptionBuilder        newSubscription()
 * @method static AnalyticsManagerService    analytics()
 */
class Wave
{
    protected static function getInstance(): WaveManagerService
    {
        return resolve(WaveManagerService::class);
    }

    /**
     * Proxy static calls to the manager
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return static::getInstance()->$method(...$args);
    }
}
