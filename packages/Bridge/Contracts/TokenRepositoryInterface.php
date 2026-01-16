<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * TokenRepository Interface defines the contract for storing and retrieving API tokens.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Contracts;

use Bridge\PersonalAccessToken;
use Helpers\DateTimeHelper;

interface TokenRepositoryInterface
{
    public function createToken(TokenableInterface $tokenable, string $name, string $hashedToken, array $abilities = ['*'], ?DateTimeHelper $expiresAt = null): PersonalAccessToken;

    public function findToken(int $id): ?PersonalAccessToken;

    public function deleteToken(int $id): bool;
}
