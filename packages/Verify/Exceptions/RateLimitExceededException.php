<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rate Limit Exceeded Exception
 *
 * Thrown when rate limits are exceeded for generation or verification
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Exceptions;

use Exception;

class RateLimitExceededException extends Exception
{
    public function __construct(string $identifier, string $type, int $limit, int $windowMinutes)
    {
        parent::__construct(
            "Rate limit exceeded for '{$identifier}'. {$type} attempts limited to {$limit} per {$windowMinutes} minutes"
        );
    }
}
