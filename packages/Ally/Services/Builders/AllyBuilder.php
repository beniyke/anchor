<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * AllyBuilder provides a fluent interface for registering resellers.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Services\Builders;

use Ally\Enums\ResellerTier;
use Ally\Models\Reseller;
use Ally\Services\AllyManagerService;

class AllyBuilder
{
    protected array $data = [];

    /**
     * Set the user associated with this reseller.
     */
    public function user(int|string $userId): self
    {
        $this->data['user_id'] = $userId;

        return $this;
    }

    /**
     * Set the reseller's company name.
     */
    public function company(string $name): self
    {
        $this->data['company_name'] = $name;

        return $this;
    }

    /**
     * Set the reseller tier to Platinum.
     */
    public function platinum(): self
    {
        return $this->tier(ResellerTier::PLATINUM);
    }

    /**
     * Set the reseller tier to Gold.
     */
    public function gold(): self
    {
        return $this->tier(ResellerTier::GOLD);
    }

    /**
     * Set the reseller tier to Standard.
     */
    public function standard(): self
    {
        return $this->tier(ResellerTier::STANDARD);
    }

    public function tier(string|ResellerTier $tier): self
    {
        $this->data['tier'] = $tier instanceof ResellerTier ? $tier->value : $tier;

        return $this;
    }

    public function status(string $status): self
    {
        $this->data['status'] = $status;

        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->data['metadata'] = array_merge($this->data['metadata'] ?? [], $metadata);

        return $this;
    }

    /**
     * Create the reseller using the manager.
     */
    public function create(): Reseller
    {
        return resolve(AllyManagerService::class)->create($this->data);
    }
}
