<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Validates static tokens (config-based).
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\ApiAuth\Validators;

use Bridge\ApiAuth\StaticUser;
use Security\Auth\Contracts\Authenticatable;

class StaticTokenValidator
{
    public function validate(string $request_token, string $expected_token): ?Authenticatable
    {
        if (hash_equals($request_token, $expected_token)) {
            return new StaticUser($expected_token);
        }

        return null;
    }
}
