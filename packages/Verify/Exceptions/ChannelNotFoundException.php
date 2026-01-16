<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Channel Not Found Exception
 *
 * Thrown when requested delivery channel is not configured
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Exceptions;

use Exception;

class ChannelNotFoundException extends Exception
{
    public function __construct(string $channel)
    {
        parent::__construct("Channel '{$channel}' is not configured or does not exist");
    }
}
