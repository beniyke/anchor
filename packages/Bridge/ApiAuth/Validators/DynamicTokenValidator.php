<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Validates dynamic API tokens (database-backed).
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\ApiAuth\Validators;

use Bridge\Models\ApiKey;
use Helpers\DateTimeHelper;

class DynamicTokenValidator
{
    public function validate(string $token): ?object
    {
        $hashedToken = hash('sha256', $token);

        $apiKey = ApiKey::query()->where('key', $hashedToken)->first();

        if ($apiKey) {
            $apiKey->update(['last_used_at' => DateTimeHelper::now()]);

            return $apiKey;
        }

        return null;
    }
}
