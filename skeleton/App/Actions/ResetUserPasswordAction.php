<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Notifications\Email\ResetPasswordEmailNotification;
use App\Requests\RecoverPasswordRequest;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Mail\Mail;

class ResetUserPasswordAction
{
    private const RESET_TOKEN_LENGTH = 64;

    private readonly int $tokenValidityMinutes;

    public function __construct(int $minutes)
    {
        $this->tokenValidityMinutes = $minutes;
    }

    public function execute(RecoverPasswordRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        $user = User::findByEmail($request->email);

        if (! $user) {
            return false;
        }

        if (! $user->isActive()) {
            return false;
        }

        $newToken = Str::random('secure', self::RESET_TOKEN_LENGTH);

        $data = [
            'reset_token' => $newToken,
            'reset_token_created_at' => DateTimeHelper::now(),
        ];

        $updateSuccess = $user->update($data);

        if (! $updateSuccess) {
            return false;
        }

        $tokenExpiration = DateTimeHelper::parse($user->reset_token_created_at)
            ->addMinutes($this->tokenValidityMinutes)->format('M d, Y \a\t h:i A');

        $emailPayload = Data::make($user->only(['name', 'email', 'reset_token']))
            ->add([
                'token_expiration' => $tokenExpiration,
            ]);

        defer(function () use ($emailPayload, $user) {
            activity('initiated password reset for {email}', ['email' => $emailPayload->get('email')], $user->id);
        });

        Mail::deferred(new ResetPasswordEmailNotification($emailPayload));

        return true;
    }
}
