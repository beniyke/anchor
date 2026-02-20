<?php

declare(strict_types=1);

namespace App\Auth\Actions;

use App\Auth\Requests\ResetPasswordRequest;
use App\Models\User;
use Helpers\Encryption\Encrypter;

class SetNewUserPasswordAction
{
    private readonly Encrypter $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function execute(User $user, ResetPasswordRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        $password = $this->encrypter->hashPassword($request->password);
        $updateSuccess = $user->updatePassword($password);

        if ($updateSuccess) {
            activity('password successfully reset by user {email}', ['email' => $user->email], $user->id);
        }

        return (bool) $updateSuccess;
    }
}
