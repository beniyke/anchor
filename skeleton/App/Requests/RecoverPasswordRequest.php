<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class RecoverPasswordRequest extends BaseRequest
{
    public readonly string $email;
}
