<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\Email\EmailChangeEmailNotification;
use App\Requests\UpdateProfileRequest;
use Database\DB;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\Encryption\Encrypter;
use Helpers\String\Str;
use RuntimeException;

class UpdateProfileAction
{
    private readonly Encrypter $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function execute(User $user, UpdateProfileRequest $request): ?Data
    {
        if (! $request->isValid()) {
            return null;
        }

        $password_is_valid = $this->encrypter->verifyPassword($request->password, $user->password);

        if (! $password_is_valid) {
            return null;
        }

        $email_has_changed = $user->emailHasChanged($request->email);
        $payload = $request->getData()->remove(['password']);

        return DB::transaction(function () use ($user, $payload, $email_has_changed) {
            $userUpdated = $user->update($payload->data());

            if (! $userUpdated) {
                throw new RuntimeException('Failed to update user payload.');
            }

            if (! $email_has_changed) {
                return Data::make(['email_changed' => false]);
            }

            $newToken = Str::random('secure');

            $updateData = [
                'status' => UserStatus::Inactive,
                'activation_token' => $newToken,
                'activation_token_created_at' => DateTimeHelper::now(),
            ];

            $userUpdatedToken = $user->update($updateData);

            if (! $userUpdatedToken) {
                throw new RuntimeException('Failed to update user status and token.');
            }

            $data = Data::make($user->only(['name', 'email', 'activation_token']));

            defer(function () use ($data) {
                activity('initiated email change to {new_email}', ['new_email' => $data->get('email')]);

                notify('email')
                    ->with(EmailChangeEmailNotification::class, $data)
                    ->send();
            });

            return Data::make(['email_changed' => true]);
        });
    }
}
