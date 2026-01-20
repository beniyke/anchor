<?php

declare(strict_types=1);

namespace App\Account\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Enums\UserStatus;
use App\Requests\UserRequest;
use Helpers\String\StrCollection;

class UserFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['name', 'email', 'role', 'status', 'gender'];
    }

    public function rules(): array
    {
        $formData = $this->getRequestData();

        return [
            'name' => [
                'type' => 'string',
                'minlength' => 3,
                'maxlength' => 255,
                'is_valid' => ['name'],
            ],
            'role' => [
                'type' => 'integer',
                'exist' => 'role.id',
            ],
            'status' => [
                'exist' => UserStatus::all(),
            ],
            'gender' => [
                'exist' => ['male', 'female'],
            ],
            'email' => [
                'type' => 'email',
                'minlength' => 10,
                'is_valid' => ['email'],
                'unique' => 'user.email:' . ($formData->has('id') ? $formData->get('id') : ''),
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'status' => 'Status',
            'gender' => 'Gender',
        ];
    }

    public function additionalData(): ?array
    {
        $requestData = $this->getRequestData();

        if ($requestData->has('status')) {
            return null;
        }

        return ['status' => UserStatus::Inactive->value];
    }

    public function transformData(array $data): array
    {
        return StrCollection::make($data)->touch([
            'email' => ['lowercase', 'clean_email'],
            'name' => ['ucwords', 'clean'],
        ])->get();
    }

    public function modify(): array
    {
        return ['role' => 'role_id'];
    }

    public function getRequest(): UserRequest
    {
        return new UserRequest($this->validated()->data());
    }
}
