<?php

declare(strict_types=1);

namespace App\Account\Requests;

use App\Core\BaseRequest;

class UserRequest extends BaseRequest
{
    public readonly string $name;

    public readonly string $email;

    public readonly string $status;

    public readonly string $gender;

    public readonly string $role;
}
