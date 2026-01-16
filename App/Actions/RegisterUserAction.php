<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserStatus;
use App\Models\User;
use App\Requests\SignupRequest;
use App\Tasks\SendSignupMailTask;
use Helpers\DateTimeHelper;
use Helpers\Encryption\Encrypter;
use Helpers\String\Str;
use Queue\Queue;

class RegisterUserAction
{
    private const ACTIVATION_TOKEN_LENGTH = 64;

    private readonly Encrypter $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
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

        $payload->add([
            'refid' => Str::random('secure'),
            'activation_token' => $activationToken,
            'activation_token_created_at' => DateTimeHelper::now(),
            'status' => UserStatus::Inactive,
            'password_updated_at' => DateTimeHelper::now(),
        ]);

        $user = User::create($payload->data());

        if (! $user) {
            return false;
        }

        $mailPayload = $user->only(['name', 'email', 'activation_token']);

        Queue::deferred(SendSignupMailTask::class, $mailPayload);

        return true;
    }
}
