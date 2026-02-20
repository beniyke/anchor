<?php

declare(strict_types=1);

namespace App\Auth\Requests;

use App\Core\BaseRequest;

class ResetPasswordRequest extends BaseRequest
{
    public readonly string $password;
}
