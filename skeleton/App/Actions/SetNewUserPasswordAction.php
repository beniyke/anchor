<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Requests\ResetPasswordRequest;
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
            defer(function () use ($user) {
                activity('password successfully reset by user {email}', ['email' => $user->email], $user->id);
            });
        }

        return (bool) $updateSuccess;
    }
}
