<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * PersonalAccessToken represents a personal access token for API authentication.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge;

use Helpers\DateTimeHelper;

class PersonalAccessToken
{
    public function __construct(
        public int $id,
        public string $tokenableType,
        public string|int $tokenableId,
        public string $name,
        public string $hashedToken,
        public array $abilities,
        public ?DateTimeHelper $expiresAt = null,
        public ?DateTimeHelper $createdAt = null,
    ) {
    }

    /**
     * Checks if the token has a specific ability.
     */
    public function can(string $ability): bool
    {
        if (empty($this->abilities) || in_array('*', $this->abilities)) {
            return true;
        }

        return in_array($ability, $this->abilities);
    }

    /**
     * Checks if the token is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt < DateTimeHelper::now();
    }
}
