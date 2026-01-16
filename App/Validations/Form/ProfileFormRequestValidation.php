<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Requests\UpdateProfileRequest;
use Helpers\String\StrCollection;

class ProfileFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['name', 'email', 'gender', 'phone', 'password'];
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
                'is_valid' => ['email'],
            ],
            'gender' => [
                'exist' => ['male', 'female'],
            ],
            'phone' => [
                'type' => 'phone',
            ],
            'password' => [
                'minlength' => 3,
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'gender' => 'Gender',
            'phone' => 'Phone',
            'password' => 'Confirm Password',
        ];
    }

    public function transformData(array $data): array
    {
        return StrCollection::make($data)->touch([
            'name' => ['ucfirst', 'clean'],
        ])->get();
    }

    public function getRequest(): UpdateProfileRequest
    {
        return new UpdateProfileRequest($this->validated()->data());
    }
}
