<?php

declare(strict_types=1);

namespace App\Account\Actions;

use App\Models\User;

class DeleteUserAction
{
    public function execute(string $refid): bool
    {
        $user = User::findByRefid($refid);

        if (! $user) {
            return false;
        }

        $email = $user->email;
        $deleted = $user->delete();

        if (! $deleted) {
            return false;
        }

        activity('deleted user {email}', compact('email'));

        return (bool) $deleted;
    }
}
