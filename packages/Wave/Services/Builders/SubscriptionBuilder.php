<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Subscription Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services\Builders;

use Exception;
use Wave\Models\Subscription;
use Wave\Services\SubscriptionManagerService;

class SubscriptionBuilder
{
    private string|int|null $ownerId = null;

    private string|null $ownerType = null;

    private string|int|null $planId = null;

    private array $options = [];

    public function __construct(
        private readonly SubscriptionManagerService $manager
    ) {
    }

    public function for(object|string|int $owner, ?string $type = null): self
    {
        if (is_object($owner)) {
            $this->ownerId = $owner->id ?? throw new Exception("Owner object must have an ID");
            $this->ownerType = $type ?? get_class($owner);

            if ($type === null && method_exists($owner, 'getMorphClass')) {
                $this->ownerType = $owner->getMorphClass();
            }
        } else {
            $this->ownerId = $owner;
            $this->ownerType = $type;
        }

        if (!$this->ownerType) {
        }

        return $this;
    }

    public function plan(string|int $planId): self
    {
        $this->planId = $planId;

        return $this;
    }

    public function trialDays(int $days): self
    {
        $this->options['trial_days'] = $days;

        return $this;
    }

    public function quantity(int $quantity): self
    {
        $this->options['quantity'] = $quantity;

        return $this;
    }

    public function meta(string $key, mixed $value): self
    {
        $this->options['metadata'][$key] = $value;

        return $this;
    }

    public function start(): Subscription
    {
        if (!$this->ownerId || !$this->ownerType) {
            throw new Exception("Subscription owner not specified.");
        }
        if (!$this->planId) {
            throw new Exception("Subscription plan not specified.");
        }

        return $this->manager->subscribe(
            $this->ownerId,
            $this->ownerType,
            $this->planId,
            $this->options
        );
    }
}
