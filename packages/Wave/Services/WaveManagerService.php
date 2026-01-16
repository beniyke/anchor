<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wave Manager for coordinating subscription billing.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Wallet\Services\WalletManagerService;
use Wave\Models\Plan;
use Wave\Models\Product;
use Wave\Models\Subscription;
use Wave\Services\Builders\SubscriptionBuilder;

class WaveManagerService
{
    public function __construct(
        private readonly PlanManagerService $planManager,
        private readonly SubscriptionManagerService $subscriptionManager,
        private readonly InvoiceManagerService $invoiceManager,
        private readonly CouponManagerService $couponManager,
        private readonly ProductManagerService $productManager,
        private readonly TaxManagerService $taxManager,
        private readonly WalletManagerService $walletManager,
        private readonly AnalyticsManagerService $analyticsManager,
        private readonly AffiliateManagerService $affiliateManager
    ) {
    }

    public function subscribe(string|int $ownerId, string $ownerType, string|int $planId, array $options = []): Subscription
    {
        return $this->subscriptionManager->subscribe($ownerId, $ownerType, $planId, $options);
    }

    public function findSubscription(string|int $id): ?Subscription
    {
        return $this->subscriptionManager->find($id);
    }

    public function findPlan(string|int $id): ?Plan
    {
        return $this->planManager->find($id);
    }

    public function plan(): PlanManagerService
    {
        return $this->planManager;
    }

    public function findProduct(string|int $id): ?Product
    {
        return $this->productManager->find($id);
    }

    public function product(): ProductManagerService
    {
        return $this->productManager;
    }

    public function createProduct(array $data): Product
    {
        return $this->productManager->create($data);
    }

    public function taxes(): TaxManagerService
    {
        return $this->taxManager;
    }

    public function subscriptions(): SubscriptionManagerService
    {
        return $this->subscriptionManager;
    }

    public function invoices(): InvoiceManagerService
    {
        return $this->invoiceManager;
    }

    public function coupons(): CouponManagerService
    {
        return $this->couponManager;
    }

    public function analytics(): AnalyticsManagerService
    {
        return $this->analyticsManager;
    }

    public function newSubscription(): SubscriptionBuilder
    {
        return new SubscriptionBuilder($this->subscriptionManager);
    }

    public function wallet(): WalletManagerService
    {
        return $this->walletManager;
    }

    public function affiliates(): AffiliateManagerService
    {
        return $this->affiliateManager;
    }
}
