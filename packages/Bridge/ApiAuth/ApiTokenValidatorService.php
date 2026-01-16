<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ApiTokenValidatorService validates API tokens against configured strategies.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\ApiAuth;

use Bridge\ApiAuth\Contracts\ApiTokenValidatorServiceInterface;
use Bridge\ApiAuth\Validators\AuthTokenValidator;
use Bridge\ApiAuth\Validators\DynamicTokenValidator;
use Bridge\ApiAuth\Validators\StaticTokenValidator;
use Core\Ioc\ContainerInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;

class ApiTokenValidatorService implements ApiTokenValidatorServiceInterface
{
    private const TYPE_STATIC = 'static';
    private const TYPE_DYNAMIC = 'dynamic';
    private const TYPE_AUTH = 'auth';

    public function __construct(private Request $request, private ConfigServiceInterface $config, private ContainerInterface $container)
    {
    }

    public function getAuthenticatedUser(): ?object
    {
        $route = $this->request->route();
        $config = $this->config->get("api.{$route}");
        $token = $this->request->getAuthToken();

        if (! $config || ! $token) {
            return null;
        }

        $type = $config['type'] ?? null;

        if ($type === self::TYPE_STATIC) {
            $validator = $this->container->get(StaticTokenValidator::class);

            return $validator->validate($token, $config['token']);
        }

        if ($type === self::TYPE_DYNAMIC) {
            $validator = $this->container->get(DynamicTokenValidator::class);

            return $validator->validate($token);
        }

        if ($type === self::TYPE_AUTH) {
            $validator = $this->container->get(AuthTokenValidator::class);

            return $validator->validate($token, $config['abilities'] ?? ['*']);
        }

        return null;
    }
}
