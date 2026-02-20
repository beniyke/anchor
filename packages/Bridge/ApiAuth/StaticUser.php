<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * StaticUser represents a user authenticated via a static token.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\ApiAuth;

use Security\Auth\Contracts\Authenticatable;

class StaticUser implements Authenticatable
{
    /**
     * The authentication type.
     */
    public string $type = 'static';

    /**
     * Create a new static user instance.
     */
    public function __construct(
        public string $token
    ) {
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthId(): int|string
    {
        return 0;
    }

    public function getAuthPassword(): string
    {
        return $this->token;
    }

    public function getAuthIdentifierName(): string
    {
        return 'token';
    }

    public function canAuthenticate(): bool
    {
        return true;
    }
}
