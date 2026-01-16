<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Requests\RecoverPasswordRequest;

class RecoverPasswordFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['email'];
    }

    public function rules(): array
    {
        return [
            'email' => [
                'type' => 'email',
                'is_valid' => ['email'],
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'email' => 'Email',
        ];
    }

    public function transformData(array $data): array
    {
        return str($data)->touch([
            'email' => ['lowercase', 'clean_email'],
        ])->get();
    }

    public function getRequest(): RecoverPasswordRequest
    {
        return new RecoverPasswordRequest($this->validated()->data());
    }
}
