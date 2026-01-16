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

class StaticTokenValidator
{
    public function validate(string $request_token, string $expected_token): ?object
    {
        if (hash_equals($request_token, $expected_token)) {
            return (object) ['token' => $expected_token, 'type' => 'static'];
        }

        return null;
    }
}
