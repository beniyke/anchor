<?php

declare(strict_types=1);

namespace App\Account\Validations\Form;

use App\Account\Requests\RoleRequest;
use App\Core\BaseRequestValidation;
use Helpers\String\StrCollection;

class RoleFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['name', 'description', 'permission'];
    }

    public function rules(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'minlength' => 3,
                'maxlength' => 100
            ],
            'description' => [
                'type' => 'string',
                'minlength' => 5,
                'maxlength' => 200
            ],
            'permission' => [
                'type' => 'string',
            ]
        ];
    }

    public function parameters(): array
    {
        return [
            'name' => 'Name',
            'description' => 'Description',
            'permission' => 'Permission'
        ];
    }

    public function transformData(array $data): array
    {
        return StrCollection::make($data)->touch([
            'name' => ['clean', 'capitalize'],
            'description' => ['clean', 'ucfirst']
        ])->get();
    }

    public function getRequest(): RoleRequest
    {
        return new RoleRequest($this->validated()->data());
    }
}
