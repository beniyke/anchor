<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\ActivateUserAction;
use App\Actions\ChangePasswordAction;
use App\Actions\ConfirmUserAction;
use App\Actions\DeleteUserAction;
use App\Actions\PreRegisterUserAction;
use App\Actions\RegisterUserAction;
use App\Actions\ResendActivationTokenAction;
use App\Actions\ResetUserPasswordAction;
use App\Actions\SetNewUserPasswordAction;
use App\Actions\UpdatePhotoAction;
use App\Actions\UpdateProfileAction;
use App\Actions\UpdateUserAction;
use App\Models\User;
use App\Requests\ChangePasswordRequest;
use App\Requests\RecoverPasswordRequest;
use App\Requests\ResetPasswordRequest;
use App\Requests\SearchUserRequest;
use App\Requests\SignupRequest;
use App\Requests\UpdateProfileRequest;
use App\Requests\UploadPhotoRequest;
use App\Requests\UserRequest;
use App\Views\Models\UserViewModel;
use Core\Services\ConfigServiceInterface;
use Database\Pagination\Paginator;
use Helpers\Data;
use Helpers\Encryption\Encrypter;

class UserService
{
    private const RESET_TOKEN_VALIDITY_MINUTES = 60;

    private readonly SessionService $sessionService;

    private Encrypter $encrypter;

    private readonly ConfigServiceInterface $config;
    private const DEFAULT_PER_PAGE = 20;

    public function __construct(SessionService $sessionService, Encrypter $encrypter, ConfigServiceInterface $config)
    {
        $this->sessionService = $sessionService;
        $this->encrypter = $encrypter;
        $this->config = $config;
    }

    public function isFirstUserSetup(): bool
    {
        return ! User::superAdmin()->exists();
    }

    public function confirmUser(Data $payload): ?User
    {
        return (new ConfirmUserAction($this->encrypter))->execute($payload);
    }

    public function changeUserPassword(User $user, ChangePasswordRequest $request): bool
    {
        return (new ChangePasswordAction($this->sessionService, $this->encrypter))->execute($user, $request);
    }

    public function setNewUserPassword(User $user, ResetPasswordRequest $request): bool
    {
        return (new SetNewUserPasswordAction($this->encrypter))->execute($user, $request);
    }

    public function updateUserProfile(User $user, UpdateProfileRequest $request): ?Data
    {
        return (new UpdateProfileAction($this->encrypter))->execute($user, $request);
    }

    public function preRegisterUser(UserRequest $request): bool
    {
        return (new PreRegisterUserAction($this->encrypter))->execute($request);
    }

    public function updateUser(User $user, UserRequest $request): bool
    {
        return (new UpdateUserAction())->execute($user, $request);
    }

    public function updateUserPhoto(User $user, UploadPhotoRequest $request): bool
    {
        return (new UpdatePhotoAction($this->config))->execute($user, $request);
    }

    public function resetUserPassword(RecoverPasswordRequest $request): bool
    {
        return (new ResetUserPasswordAction(self::RESET_TOKEN_VALIDITY_MINUTES))->execute($request);
    }

    public function resendActivationToken(?string $refid = null): bool
    {
        if (empty($refid)) {
            return false;
        }

        return (new ResendActivationTokenAction($this->encrypter))->execute($refid);
    }

    public function registerUser(SignupRequest $request): bool
    {
        return (new RegisterUserAction($this->encrypter))->execute($request);
    }

    public function activateUser(string $activation_token): bool
    {
        return (new ActivateUserAction())->execute($activation_token);
    }

    public function deleteUser(?string $refid = null): bool
    {
        if (empty($refid)) {
            return false;
        }

        return (new DeleteUserAction())->execute($refid);
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

    public function listUsers(SearchUserRequest $request, $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $users = User::with('role')
            ->when($request->search, function ($query) use ($request) {
                return $query->search($request->search);
            })
            ->nonSuperAdmin()
            ->latest()
            ->paginate($perPage, $page);

        $users->setItems(UserViewModel::collection($users->items(), ['role']));

        return $users;
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function getUser(?string $refid = null): ?User
    {
        if (empty($refid)) {
            return null;
        }

        return User::findByRefid($refid);
    }
}
