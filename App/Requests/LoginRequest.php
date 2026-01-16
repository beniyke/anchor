<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class LoginRequest extends BaseRequest
{
    public readonly string $email;

    public readonly string $password;

    public function hasRememberMe(): bool
    {
        return false;
    }
}
