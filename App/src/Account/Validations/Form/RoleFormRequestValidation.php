<?php

declare(strict_types=1);

namespace App\Account\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Enums\RoleType;
use App\Requests\RoleRequest;
use Helpers\String\StrCollection;

class RoleFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['title', 'permission', 'type'];
    }

    public function rules(): array
    {
        return [
            'title' => [
                'type' => 'string',
            ],
            'permission' => [
                'type' => 'string',
            ],
            'type' => [
                'exist' => RoleType::all(),
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'title' => 'Title',
            'permission' => 'Permission',
            'type' => 'Type',
        ];
    }

    public function transformData(array $data): array
    {
        return StrCollection::make($data)->touch([
            'title' => ['clean', 'capitalize'],
        ])->get();
    }

    public function getRequest(): RoleRequest
    {
        return new RoleRequest($this->validated()->data());
    }
}
