<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for models that can have API tokens.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Contracts;

use Bridge\PersonalAccessToken;

interface TokenableInterface
{
    public function getTokenableId(): int|string;

    public function getTokenableType(): string;

    public function withAccessToken(PersonalAccessToken $token): self;

    public function currentAccessToken(): ?PersonalAccessToken;

    /**
     * Determine if the current access token has a specific ability.
     */
    public function tokenCan(string $ability): bool;
}
