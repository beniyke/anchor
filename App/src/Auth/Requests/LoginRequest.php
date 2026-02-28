<?php

declare(strict_types=1);

namespace App\Auth\Requests;

use App\Core\BaseRequest;
use Security\Auth\Contracts\ProvidesRememberMe;

class LoginRequest extends BaseRequest implements ProvidesRememberMe
{
    public readonly string $email;

    public readonly string $password;

    public function hasRememberMe(): bool
    {
        return false;
    }
}
