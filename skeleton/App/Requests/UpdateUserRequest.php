<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    public readonly string $email;

    public readonly int $role_id;
}
