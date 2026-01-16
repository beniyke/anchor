<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for the API Token Validator Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\ApiAuth\Contracts;

interface ApiTokenValidatorServiceInterface
{
    public function getAuthenticatedUser(): ?object;
}
