<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;
use Helpers\Http\FileHandler;

class UploadPhotoRequest extends BaseRequest
{
    public readonly FileHandler $photo;
}
