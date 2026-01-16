<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Low Credit Event.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Events;

use Ally\Models\Reseller;

class LowCreditEvent
{
    public function __construct(
        public Reseller $reseller,
        public int $balance,
        public int $threshold
    ) {
    }
}
