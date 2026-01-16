<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Wave package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Providers;

use Core\Event;
use Core\Services\ConfigServiceInterface;
use Core\Services\ServiceProvider;
use Pay\Events\PaymentSuccessfulEvent;
use Wave\Listeners\ProcessPaymentSuccessListener;
use Wave\Models\Affiliate;
use Wave\Models\Coupon;
use Wave\Models\Discount;
use Wave\Models\Invoice;
use Wave\Models\InvoiceItem;
use Wave\Models\Plan;
use Wave\Models\Product;
use Wave\Models\Referral;
use Wave\Models\Subscription;
use Wave\Models\TaxRate;
use Wave\Services\AffiliateManagerService;
use Wave\Services\CouponManagerService;
use Wave\Services\InvoiceManagerService;
use Wave\Services\PlanManagerService;
use Wave\Services\SubscriptionManagerService;
use Wave\Services\WaveManagerService;

class WaveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(PlanManagerService::class);
        $this->container->singleton(SubscriptionManagerService::class);
        $this->container->singleton(InvoiceManagerService::class);
        $this->container->singleton(CouponManagerService::class);
        $this->container->singleton(AffiliateManagerService::class);
        $this->container->singleton(WaveManagerService::class);

        // Load config
        $config = $this->container->get(ConfigServiceInterface::class);
        $configPath = __DIR__ . '/../Config/wave.php';
        if (file_exists($configPath)) {
            $waveConfig = include $configPath;
            foreach ($waveConfig as $key => $value) {
                $config->set("wave.{$key}", $value);
            }
        }
    }

    public function boot(): void
    {
        $models = [
            Plan::class,
            Subscription::class,
            Invoice::class,
            InvoiceItem::class,
            Coupon::class,
            Discount::class,
            Product::class,
            TaxRate::class,
            Affiliate::class,
            Referral::class,
        ];

        foreach ($models as $model) {
            $model::creating(function ($instance) {});
        }

        Event::listen(PaymentSuccessfulEvent::class, ProcessPaymentSuccessListener::class);
    }
}
