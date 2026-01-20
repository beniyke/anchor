<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class UserRequest extends BaseRequest
{
    public readonly string $name;

    public readonly string $email;

    public readonly string $status;

    public readonly string $gender;

    public readonly int $role_id;
}
