<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\Email\AccountVerificationEmailNotification;
use App\Requests\UserRequest;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\Encryption\Encrypter;
use Helpers\String\Str;
use Mail\Mail;

class PreRegisterUserAction
{
    private const ACTIVATION_TOKEN_LENGTH = 64;

    private readonly Encrypter $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function execute(UserRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        $payload = $request->getData();
        $generatedPassword = Str::password(8, ['letters' => true, 'numbers' => true, 'symbols' => false, 'spaces' => false]);
        $activationToken = Str::random('secure', self::ACTIVATION_TOKEN_LENGTH);

        $payload->add([
            'refid' => Str::random('secure'),
            'password' => $this->encrypter->hashPassword($generatedPassword),
            'activation_token' => $activationToken,
            'activation_token_created_at' => DateTimeHelper::now(),
            'status' => UserStatus::Inactive,
        ]);

        $user = User::create($payload->data());

        if (! $user) {
            return false;
        }

        $payloadData = $user->only(['name', 'email', 'activation_token']);
        $payloadData['password'] = $generatedPassword;

        $emailPayload = Data::make($payloadData);

        Mail::deferred(new AccountVerificationEmailNotification($emailPayload));

        defer(function () use ($emailPayload) {
            activity('created account for {user}', ['user' => $emailPayload->get('name')]);
        });

        return true;
    }
}
