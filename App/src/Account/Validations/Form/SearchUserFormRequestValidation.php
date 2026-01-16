<?php

declare(strict_types=1);

namespace App\Account\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Enums\UserStatus;
use App\Requests\SearchUserRequest;

class SearchUserFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['search', 'status'];
    }

    public function include(): array
    {
        return ['search', 'status'];
    }

    public function notempty(): array
    {
        return [
            'search' => [
                'rules' => [
                    'search' => [
                        'type' => 'string',
                        'minlength' => 3,
                        'contains_any_valid' => ['name', 'email'],
                    ],
                ],
                'parameters' => [
                    'search' => 'Search',
                ],
            ],
            'status' => [
                'rules' => [
                    'status' => [
                        'exist' => UserStatus::all(),
                    ],
                ],
                'parameters' => [
                    'status' => 'Status',
                ],
            ],
        ];
    }

    public function getRequest(): SearchUserRequest
    {
        return new SearchUserRequest($this->validated()->data());
    }
}
