<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Notifications\Email\AccountVerificationEmailNotification;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\Encryption\Encrypter;
use Helpers\String\Str;
use Mail\Mail;

class ResendActivationTokenAction
{
    private const ACTIVATION_TOKEN_VALIDITY_MINUTES = 2880;

    private readonly Encrypter $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function execute(string $refid): bool
    {
        $user = User::findByRefid($refid);

        if (! $user) {
            return false;
        }

        if (! $user->hasActivationToken()) {
            return false;
        }

        $generatedPassword = Str::password(8, ['letters' => true, 'numbers' => true, 'symbols' => false, 'spaces' => false]);
        $isTokenValid = $user->activationTokenValid(self::ACTIVATION_TOKEN_VALIDITY_MINUTES);

        if (! $isTokenValid) {
            $user->update([
                'activation_token' => Str::random('secure'),
                'activation_token_created_at' => DateTimeHelper::now(),
                'password' => $this->encrypter->hashPassword($generatedPassword),
            ]);
        } else {
            $user->update([
                'password' => $this->encrypter->hashPassword($generatedPassword),
            ]);
        }

        $payload = Data::make($user->only(['name', 'email', 'activation_token']))
            ->add(['password' => $generatedPassword]);

        Mail::deferred(new AccountVerificationEmailNotification($payload));

        defer(function () use ($user) {
            activity('resent {user} account activation link to {email}', ['user' => $user->name, 'email' => $user->email]);
        });

        return true;
    }
}
