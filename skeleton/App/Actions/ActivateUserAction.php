<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

class ActivateUserAction
{
    private const ACTIVATION_TOKEN_VALIDITY_MINUTES = 2880;

    public function execute(string $activation_token): bool
    {
        $user = User::query()
            ->whereActivationToken($activation_token)
            ->activationTokenValid(self::ACTIVATION_TOKEN_VALIDITY_MINUTES)
            ->first();

        if (! $user) {
            return false;
        }

        return $user->activate();
    }
}
