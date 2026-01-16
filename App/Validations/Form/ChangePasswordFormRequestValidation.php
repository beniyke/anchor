<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Requests\ChangePasswordRequest;

class ChangePasswordFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['new_password', 'old_password', 'confirm_password', 'name'];
    }

    public function rules(): array
    {
        return [
            'old_password' => [
                'type' => 'string',
                'type' => 'password',
            ],
            'new_password' => [
                'type' => 'password',
                'same' => 'confirm_password',
                'not_same' => 'old_password',
                'not_contain' => 'name',
                'confirm' => 'name',
                'config' => [
                    'length_min' => 6,
                    'not_common' => true,
                ],
            ],
            'confirm_password' => [
                'type' => 'password',
                'same' => 'new_password',
                'not_same' => 'old_password',
                'not_contain' => 'name',
                'confirm' => 'name',
                'config' => [
                    'length_min' => 6,
                    'not_common' => true,
                ],
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'old_password' => 'Old Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm New Password',
            'name' => 'Name',
        ];
    }

    public function exclude(): array
    {
        return ['confirm_password', 'name'];
    }

    public function additionalData(): array
    {
        $user = $this->auth?->user();

        return [
            'name' => $user->name,
        ];
    }

    public function getRequest(): ChangePasswordRequest
    {
        return new ChangePasswordRequest($this->validated()->data());
    }
}
