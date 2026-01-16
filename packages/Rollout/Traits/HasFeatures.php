<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * HasFeatures trait for User model integration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Traits;

use Rollout\Rollout;

trait HasFeatures
{
    public function hasFeature(string $feature): bool
    {
        return Rollout::isEnabled($feature, $this);
    }

    public function lacksFeature(string $feature): bool
    {
        return !$this->hasFeature($feature);
    }

    public function hasAnyFeature(array $features): bool
    {
        foreach ($features as $feature) {
            if ($this->hasFeature($feature)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllFeatures(array $features): bool
    {
        foreach ($features as $feature) {
            if (!$this->hasFeature($feature)) {
                return false;
            }
        }

        return true;
    }
}
