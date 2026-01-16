<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Validates Bridge personal access tokens.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\ApiAuth\Validators;

use Bridge\Contracts\TokenableInterface;
use Bridge\TokenManager;

class AuthTokenValidator
{
    public function __construct(
        private TokenManager $tokenManager
    ) {
    }

    /**
     * Validate a Bridge personal access token.
     */
    public function validate(string $token, array $requiredAbilities = ['*']): ?TokenableInterface
    {
        $tokenable = $this->tokenManager->authenticate($token, function (string $type, int $id) {
            if (! class_exists($type)) {
                return null;
            }

            if (method_exists($type, 'find')) {
                return $type::find($id);
            }

            return null;
        });

        if (! $tokenable) {
            return null;
        }

        if (! in_array('*', $requiredAbilities)) {
            foreach ($requiredAbilities as $ability) {
                if (! $this->tokenManager->checkAbility($token, $ability)) {
                    return null;
                }
            }
        }

        return $tokenable;
    }
}
