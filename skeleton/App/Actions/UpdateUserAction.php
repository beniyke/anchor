<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\Email\EmailChangeEmailNotification;
use App\Requests\UserRequest;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Mail\Mail;

class UpdateUserAction
{
    public function execute(User $user, UserRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        $payload = $request->getData();

        $oldEmail = $user->email;
        $newEmail = $payload->get('email');
        $oldRoleTitle = $user->role?->title ?? 'Super Admin';

        $emailHasChanged = $user->emailHasChanged($newEmail);
        $updated = $user->update($payload->data());

        if (! $updated) {
            return false;
        }

        if ($emailHasChanged) {
            $updateData = [
                'status' => UserStatus::Inactive,
                'activation_token' => Str::random('secure'),
                'activation_token_created_at' => DateTimeHelper::now(),
            ];

            $user->update($updateData);
            $data = Data::make($user->only(['name', 'email', 'activation_token']));

            Mail::deferred(new EmailChangeEmailNotification($data));
        }

        $user->refresh();
        $newRoleTitle = $user->role?->title ?? 'Super Admin';

        defer(function () use ($user, $oldRoleTitle, $oldEmail, $newRoleTitle, $newEmail) {
            $changes = [];

            if ($oldEmail !== $newEmail) {
                $changes[] = "email from '{$oldEmail}' to '{$newEmail}'";
            }

            if ($oldRoleTitle !== $newRoleTitle) {
                $changes[] = "role from '{$oldRoleTitle}' to '{$newRoleTitle}'";
            }

            if (! empty($changes)) {
                $message = 'updated {user} details: ' . implode(', ', $changes);

                activity($message, ['user' => $user->name]);
            }
        });

        return true;
    }
}
