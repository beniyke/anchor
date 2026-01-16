<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * The TokenManager manages the creation, authentication, and validation of API tokens.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge;

use Bridge\Contracts\TokenableInterface;
use Bridge\Contracts\TokenRepositoryInterface;
use Helpers\DateTimeHelper;
use Helpers\String\Str;

class TokenManager
{
    protected const TOKEN_SEPARATOR = '|';

    protected TokenRepositoryInterface $repository;

    public function __construct(TokenRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createToken(TokenableInterface $tokenable, string $name, array $abilities = ['*'], ?int $expiresInSeconds = null): string
    {
        $secretToken = Str::random('secure', 20);
        $hashedToken = hash('sha256', $secretToken);
        $expiresAt = $expiresInSeconds !== null
            ? DateTimeHelper::now()->addSeconds($expiresInSeconds)
            : null;

        $accessToken = $this->repository->createToken($tokenable, $name, $hashedToken, $abilities, $expiresAt);

        return $accessToken->id . self::TOKEN_SEPARATOR . $secretToken;
    }

    public function authenticate(string $plainTextToken, callable $tokenableFinder): ?TokenableInterface
    {
        $parts = explode(self::TOKEN_SEPARATOR, $plainTextToken, 2);

        if (count($parts) !== 2 || ! is_numeric($parts[0])) {
            return null;
        }

        [$tokenId, $secretToken] = $parts;

        $accessToken = $this->repository->findToken((int) $tokenId);

        if (! $accessToken) {
            return null;
        }

        if ($accessToken->isExpired()) {
            $this->repository->deleteToken($accessToken->id);

            return null;
        }

        $receivedHash = hash('sha256', $secretToken);

        if (! hash_equals($accessToken->hashedToken, $receivedHash)) {
            return null;
        }

        $tokenable = $tokenableFinder($accessToken->tokenableType, $accessToken->tokenableId);

        if ($tokenable instanceof TokenableInterface) {
            $tokenable->withAccessToken($accessToken);
        }

        return $tokenable;
    }

    public function checkAbility(string $plainTextToken, string $requiredAbility): bool
    {
        $parts = explode(self::TOKEN_SEPARATOR, $plainTextToken, 2);

        if (count($parts) !== 2 || ! is_numeric($parts[0])) {
            return false;
        }

        [$tokenId, $secretToken] = $parts;
        $accessToken = $this->repository->findToken((int) $tokenId);

        if (! $accessToken) {
            return false;
        }

        $receivedHash = hash('sha256', $secretToken);

        if (! hash_equals($accessToken->hashedToken, $receivedHash)) {
            return false;
        }

        if ($accessToken->isExpired()) {
            return false;
        }

        return $accessToken->can($requiredAbility);
    }

    public function revokeToken(int $tokenId): bool
    {
        return $this->repository->deleteToken($tokenId);
    }
}
