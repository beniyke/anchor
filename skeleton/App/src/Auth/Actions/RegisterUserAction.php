<?php

declare(strict_types=1);

namespace App\Auth\Actions;

use App\Auth\Requests\SignupRequest;
use App\Auth\Tasks\SendSignupMailTask;
use App\Enums\UserStatus;
use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\DateTimeHelper;
use Helpers\Encryption\Encrypter;
use Helpers\String\Str;
use Mail\Mail;
use Permit\Permit;

class RegisterUserAction
{
    private const ACTIVATION_TOKEN_LENGTH = 64;

    private readonly Encrypter $encrypter;

    private readonly ConfigServiceInterface $config;

    public function __construct(Encrypter $encrypter, ConfigServiceInterface $config)
    {
        $this->encrypter = $encrypter;
        $this->config = $config;
    }

    public function execute(SignupRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        $payload = $request->getData();
        $activationToken = Str::random('secure', self::ACTIVATION_TOKEN_LENGTH);

        $payload->update([
            'password' => fn ($password) => $this->encrypter->hashPassword($password),
        ]);

        $isFirstUser = ! User::query()->exists();

        $payload->add([
            'refid' => Str::refid(),
            'activation_token' => $isFirstUser ? null : $activationToken,
            'activation_token_created_at' => $isFirstUser ? null : DateTimeHelper::now(),
            'status' => $isFirstUser ? UserStatus::Active : UserStatus::Inactive,
            'password_updated_at' => DateTimeHelper::now(),
        ]);

        $user = User::create($payload->data());

        if (! $user) {
            return false;
        }

        if ($isFirstUser) {
            $this->setupSuperAdmin($user);
        } else {
            $payload = $user->only(['name', 'email', 'activation_token']);
            Mail::queue(SendSignupMailTask::class, $payload);
        }

        return true;
    }

    private function setupSuperAdmin(User $user): void
    {
        $roleSlug = $this->config->get('permit.super_admin_role', 'super-admin');
        $permissionRegistry = $this->config->get('permissions', []);

        $allSlugs = [];
        foreach ($permissionRegistry as $perms) {
            $allSlugs = array_merge($allSlugs, array_keys($perms));
        }

        Permit::role()
            ->slug($roleSlug)
            ->name('Super Admin')
            ->description('Full system access bypass.')
            ->permissions($allSlugs)
            ->assign($user)
            ->create();

        activity('initialized system with super-admin: {user}', ['user' => $user->email], $user->id);
    }
}
