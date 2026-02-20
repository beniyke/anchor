<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * BridgeServiceProvider registers bindings for the Bridge package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Providers;

use App\Models\User;
use Bridge\ApiAuth\ApiTokenValidatorService;
use Bridge\ApiAuth\Contracts\ApiTokenValidatorServiceInterface;
use Bridge\ApiAuth\Validators\AuthTokenValidator;
use Bridge\ApiAuth\Validators\DynamicTokenValidator;
use Bridge\ApiAuth\Validators\StaticTokenValidator;
use Bridge\Contracts\TokenRepositoryInterface;
use Bridge\Repositories\DatabaseTokenRepository;
use Bridge\TokenManager;
use Core\Services\ServiceProvider;
use Security\Auth\Interfaces\AccessTokenInterface;
use Security\Auth\Interfaces\TokenManagerInterface;

class BridgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(TokenRepositoryInterface::class, DatabaseTokenRepository::class);

        $this->container->singleton(TokenManagerInterface::class, function ($container) {
            return new TokenManager($container->get(TokenRepositoryInterface::class));
        });

        $this->container->singleton(TokenManager::class, function ($container) {
            return $container->get(TokenManagerInterface::class);
        });

        $this->container->singleton(StaticTokenValidator::class);
        $this->container->singleton(DynamicTokenValidator::class);
        $this->container->singleton(AuthTokenValidator::class);

        $this->container->bind(ApiTokenValidatorServiceInterface::class, ApiTokenValidatorService::class);
    }

    public function boot(): void
    {
        $this->registerUserMacros();
    }

    protected function registerUserMacros(): void
    {
        $container = $this->container;

        User::macro('currentAccessToken', function () {
            return $this->accessToken ?? null;
        });

        User::macro('withAccessToken', function (AccessTokenInterface $token) {
            $this->accessToken = $token;

            return $this;
        });

        User::macro('tokenCan', function (string $ability) {
            $token = $this->currentAccessToken();

            return $token ? $token->can($ability) : false;
        });

        User::macro('getTokenableId', function () {
            return (int) $this->id;
        });

        User::macro('getTokenableType', function () {
            return static::class;
        });

        User::macro('createToken', function (string $name, array $abilities = ['*'], ?int $expiresInSeconds = null) use ($container) {
            return $container->get(TokenManagerInterface::class)->createToken($this, $name, $abilities, $expiresInSeconds);
        });

        User::macro('tokens', function () use ($container) {
            return $container->get(TokenRepositoryInterface::class)->findTokensByTokenable($this);
        });

        User::macro('revokeToken', function (int $tokenId) use ($container) {
            return $container->get(TokenRepositoryInterface::class)->deleteToken($tokenId);
        });

        User::macro('revokeAllTokens', function () use ($container) {
            return $container->get(TokenRepositoryInterface::class)->revokeAllTokens($this);
        });
    }
}
