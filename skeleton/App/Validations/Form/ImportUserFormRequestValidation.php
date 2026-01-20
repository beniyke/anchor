<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;

class ImportUserFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['document', 'role'];
    }

    public function rules(): array
    {
        return [
            'document' => [
                'allowed_file_size' => '1mb',
                'allowed_file_type' => ['xlsx'],
            ],
            'role' => [
                'type' => 'string',
            ],
        ];
    }

    public function parameters(): array
    {
        return [
            'role' => 'Role',
        ];
    }

    public function file(): array
    {
        return ['document' => 'Document'];
    }
}
