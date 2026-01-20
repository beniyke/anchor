<?php

declare(strict_types=1);

namespace App\Validations\Form;

use App\Core\BaseRequestValidation;
use App\Requests\UploadPhotoRequest;

class ChangePhotoFormRequestValidation extends BaseRequestValidation
{
    public function expected(): array
    {
        return ['photo'];
    }

    public function rules(): array
    {
        return [
            'photo' => [
                'allowed_file_size' => '500kb',
                'allowed_file_type' => ['jpg', 'jpeg', 'png'],
            ],
        ];
    }

    public function file(): array
    {
        return ['photo' => 'Profile Photo'];
    }

    public function getRequest(): UploadPhotoRequest
    {
        return new UploadPhotoRequest($this->validated()->data());
    }
}
