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

use Bridge\ApiAuth\ApiTokenValidatorService;
use Bridge\ApiAuth\Contracts\ApiTokenValidatorServiceInterface;
use Bridge\ApiAuth\Validators\AuthTokenValidator;
use Bridge\ApiAuth\Validators\DynamicTokenValidator;
use Bridge\ApiAuth\Validators\StaticTokenValidator;
use Bridge\Contracts\TokenRepositoryInterface;
use Bridge\Repositories\DatabaseTokenRepository;
use Bridge\TokenManager;
use Core\Services\ServiceProvider;

class BridgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(TokenRepositoryInterface::class, DatabaseTokenRepository::class);

        $this->container->singleton(TokenManager::class, function ($container) {
            return new TokenManager($container->get(TokenRepositoryInterface::class));
        });

        $this->container->singleton(StaticTokenValidator::class);
        $this->container->singleton(DynamicTokenValidator::class);
        $this->container->singleton(AuthTokenValidator::class);

        $this->container->bind(ApiTokenValidatorServiceInterface::class, ApiTokenValidatorService::class);
    }
}
