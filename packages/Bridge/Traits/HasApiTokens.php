<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Trait for adding API token functionality to models.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Traits;

use Bridge\Contracts\TokenRepositoryInterface;
use Security\Auth\Contracts\Tokenable;
use Security\Auth\Interfaces\AccessTokenInterface;
use Security\Auth\Interfaces\TokenManagerInterface;

/**
 * @implements Tokenable
 * Requires the implementing class to have 'id' property.
 */
trait HasApiTokens
{
    /**
     * The access token the user is currently using.
     */
    protected ?AccessTokenInterface $accessToken = null;

    public function currentAccessToken(): ?AccessTokenInterface
    {
        return $this->accessToken;
    }

    public function withAccessToken(AccessTokenInterface $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Determine if the current access token has a specific ability.
     */
    public function tokenCan(string $ability): bool
    {
        return $this->accessToken ? $this->accessToken->can($ability) : false;
    }

    public function getTokenableId(): int
    {
        return (int) $this->id;
    }

    public function getTokenableType(): string
    {
        return static::class;
    }

    /**
     * Create a new personal access token for the model.
     */
    public function createToken(string $name, array $abilities = ['*'], ?int $expiresInSeconds = null): string
    {
        $manager = $this->resolveTokenManager();

        return $manager->createToken($this, $name, $abilities, $expiresInSeconds);
    }

    public function tokens(): array
    {
        $repository = resolve(TokenRepositoryInterface::class);

        return $repository->findTokensByTokenable($this);
    }

    /**
     * Revoke a specific token by ID.
     */
    public function revokeToken(int $tokenId): bool
    {
        $repository = resolve(TokenRepositoryInterface::class);

        return $repository->deleteToken($tokenId);
    }

    /**
     * Revoke all tokens for this tokenable.
     */
    public function revokeAllTokens(): int
    {
        $repository = resolve(TokenRepositoryInterface::class);

        return $repository->revokeAllTokens($this);
    }

    /**
     * Resolve the TokenManager from the IoC container.
     */
    protected function resolveTokenManager(): TokenManagerInterface
    {
        return resolve(TokenManagerInterface::class);
    }
}
