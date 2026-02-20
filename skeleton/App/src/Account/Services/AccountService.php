<?php

declare(strict_types=1);

namespace App\Account\Services;

use App\Account\Actions\ChangePasswordAction;
use App\Account\Actions\DeleteUserAction;
use App\Account\Actions\PreRegisterUserAction;
use App\Account\Actions\ResendActivationTokenAction;
use App\Account\Actions\UpdatePhotoAction;
use App\Account\Actions\UpdateProfileAction;
use App\Account\Actions\UpdateUserAction;
use App\Account\Actions\UpdateUserPermissionAction;
use App\Account\Requests\ChangePasswordRequest;
use App\Account\Requests\PermissionRequest;
use App\Account\Requests\SearchUserRequest;
use App\Account\Requests\UpdateProfileRequest;
use App\Account\Requests\UploadPhotoRequest;
use App\Account\Requests\UserRequest;
use App\Models\User;
use App\Services\SessionService;
use Core\Services\ConfigServiceInterface;
use Database\Pagination\Paginator;
use Helpers\Data\Data;
use Helpers\Encryption\Encrypter;

class AccountService
{
    private const DEFAULT_PER_PAGE = 20;

    public function __construct(
        private readonly SessionService $sessionService,
        private readonly Encrypter $encrypter,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function changeUserPassword(User $user, ChangePasswordRequest $request): bool
    {
        return (new ChangePasswordAction($this->sessionService, $this->encrypter))->execute($user, $request);
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

    public function updateUserPermissions(User $user, PermissionRequest $request): bool
    {
        return (new UpdateUserPermissionAction())->execute($user, $request);
    }

    public function resendActivationToken(?string $refid = null): bool
    {
        if (empty($refid)) {
            return false;
        }

        return (new ResendActivationTokenAction($this->encrypter))->execute($refid);
    }

    public function deleteUser(?string $refid = null): bool
    {
        if (empty($refid)) {
            return false;
        }

        return (new DeleteUserAction())->execute($refid);
    }

    public function listUsers(SearchUserRequest $request, $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $users = User::query()
            ->when($request->search, function ($query) use ($request) {
                return $query->search($request->search);
            })
            ->latest()
            ->paginate($perPage, $page);

        return $users;
    }

    public function getUser(?string $refid = null): ?User
    {
        if (empty($refid)) {
            return null;
        }

        return User::findByRefid($refid);
    }
}
