<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Requests\ResetPasswordRequest;

class ResetPasswordFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['new_password', 'confirm_password'];
    }

    public function rules(): array
    {
        return [
            'new_password' => [
                'type' => 'password',
                'config' => [
                    'length_min' => 6,
                    'not_common' => true,
                ],
            ],
            'confirm_password' => [
                'same' => 'new_password',
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'new_password' => 'New password',
            'confirm_password' => 'Confirm password',
        ];
    }

    public function modify(): array
    {
        return ['new_password' => 'password'];
    }

    public function exclude(): array
    {
        return ['confirm_password'];
    }

    public function getRequest(): ResetPasswordRequest
    {
        return new ResetPasswordRequest($this->validated()->data());
    }
}
