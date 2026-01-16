<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Configurable sampling mechanism for Watcher.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Sampling;

use Watcher\Config\WatcherConfig;

class Sampler
{
    private WatcherConfig $config;

    public function __construct(WatcherConfig $config)
    {
        $this->config = $config;
    }

    public function shouldSample(string $type): bool
    {
        $rate = $this->config->getSamplingRate($type);

        if ($rate >= 1.0) {
            return true;
        }

        if ($rate <= 0.0) {
            return false;
        }

        return (mt_rand() / mt_getrandmax()) < $rate;
    }
}
