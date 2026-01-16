<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Helpers\Data;
use Helpers\Encryption\Encrypter;

class ConfirmUserAction
{
    private readonly Encrypter $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function execute(Data $payload): ?User
    {
        $user = User::findByEmailForAuth($payload->get('email'));

        if (! $user) {
            return null;
        }

        $password_is_valid = $this->encrypter->verifyPassword($payload->get('password'), $user->password);

        if (! $password_is_valid) {
            return null;
        }

        if (! $user->canLogin()) {
            return null;
        }

        return $user;
    }
}
