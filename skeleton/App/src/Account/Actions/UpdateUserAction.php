<?php

declare(strict_types=1);

namespace App\Account\Actions;

use App\Account\Notifications\Email\EmailChangeEmailNotification;
use App\Account\Requests\UserRequest;
use App\Enums\UserStatus;
use App\Models\User;
use Helpers\Data\Data;
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
        $oldRoles = $user->roleNames();

        $emailHasChanged = $user->emailHasChanged($newEmail);
        $updated = $user->update($payload->data());

        if (! $updated) {
            return false;
        }

        // Update Role via Permit
        if ($request->role) {
            $user->syncRoles([$request->role]);
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
        $newRoles = $user->roleNames();

        $changes = [];

        if ($oldEmail !== $newEmail) {
            $changes[] = "email from '{$oldEmail}' to '{$newEmail}'";
        }

        if ($oldRoles !== $newRoles) {
            $changes[] = "roles from '{$oldRoles}' to '{$newRoles}'";
        }

        if (! empty($changes)) {
            $message = 'updated {user} details: ' . implode(', ', $changes);

            activity($message, ['user' => $user->name]);
        }

        return true;
    }
}
