<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Requests\SignupRequest;

class SignupFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['name', 'gender', 'email', 'password', 'confirm_password'];
    }

    public function rules(): array
    {
        return [
            'name' => [
                'minlength' => 3,
                'is_valid' => ['name'],
            ],
            'email' => [
                'type' => 'email',
                'unique' => 'user.email',
                'is_valid' => ['email'],
            ],
            'password' => [
                'type' => 'password',
                'not_contain' => 'name',
                'confirm' => 'name',
                'config' => [
                    'length_min' => 6,
                    'not_common' => true,
                ],
            ],
            'confirm_password' => ['same' => 'password'],
            'gender' => ['exist' => ['male', 'female']],
        ];
    }

    public function parameters(): array
    {
        return [
            'name' => 'Name',
            'gender' => 'Gender',
            'email' => 'Email Address',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
        ];
    }

    public function exclude(): array
    {
        return ['confirm_password'];
    }

    public function transformData(array $data): array
    {
        return str($data)->touch([
            'email' => ['strtolower', 'clean_email'],
            'name' => ['ucfirst', 'clean', 'strip_tags'],
        ])->get();
    }

    public function getRequest(): SignupRequest
    {
        return new SignupRequest($this->validated()->data());
    }
}
