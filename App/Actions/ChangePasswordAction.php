<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Requests\ChangePasswordRequest;
use App\Services\SessionService;
use Helpers\Encryption\Encrypter;

class ChangePasswordAction
{
    protected readonly SessionService $sessionService;

    private readonly Encrypter $encrypter;

    public function __construct(SessionService $sessionService, Encrypter $encrypter)
    {
        $this->sessionService = $sessionService;
        $this->encrypter = $encrypter;
    }

    public function execute(User $user, ChangePasswordRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        $password_is_valid = $this->encrypter->verifyPassword($request->old_password, $user->password);

        if (! $password_is_valid) {
            return false;
        }

        $password = $this->encrypter->hashPassword($request->new_password);
        $password_updated = $user->updatePassword($password);

        if (! $password_updated) {
            return false;
        }

        $userId = $user->id;
        $this->sessionService->terminateAllUserSessions($userId);

        defer(function () use ($userId) {
            activity('changed password', user_id: $userId);
        });

        return true;
    }
}
