<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class SignupRequest extends BaseRequest
{
    public readonly string $name;

    public readonly string $gender;

    public readonly string $email;

    public readonly string $password;
}
