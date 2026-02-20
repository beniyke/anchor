<?php

declare(strict_types=1);

namespace App\Auth\Services;

use App\Auth\Actions\ActivateUserAction;
use App\Auth\Actions\RegisterUserAction;
use App\Auth\Actions\ResetUserPasswordAction;
use App\Auth\Actions\SetNewUserPasswordAction;
use App\Auth\Requests\RecoverPasswordRequest;
use App\Auth\Requests\ResetPasswordRequest;
use App\Auth\Requests\SignupRequest;
use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\Encryption\Encrypter;

class IdentityService
{
    private const RESET_TOKEN_VALIDITY_MINUTES = 60;

    private readonly Encrypter $encrypter;

    private readonly ConfigServiceInterface $config;

    public function __construct(Encrypter $encrypter, ConfigServiceInterface $config)
    {
        $this->encrypter = $encrypter;
        $this->config = $config;
    }

    public function isFirstUserSetup(): bool
    {
        return ! User::query()->exists();
    }

    public function registerUser(SignupRequest $request): bool
    {
        return (new RegisterUserAction($this->encrypter, $this->config))->execute($request);
    }

    public function activateUser(string $activation_token): bool
    {
        return (new ActivateUserAction())->execute($activation_token);
    }

    public function resetUserPassword(RecoverPasswordRequest $request): bool
    {
        return (new ResetUserPasswordAction(self::RESET_TOKEN_VALIDITY_MINUTES))->execute($request);
    }

    public function setNewUserPassword(User $user, ResetPasswordRequest $request): bool
    {
        return (new SetNewUserPasswordAction($this->encrypter))->execute($user, $request);
    }

    public function getUserByValidResetToken(?string $token = null): ?User
    {
        if (empty($token)) {
            return null;
        }

        return User::query()
            ->whereResetToken($token)
            ->resetTokenValid(self::RESET_TOKEN_VALIDITY_MINUTES)
            ->first();
    }
}
