<?php

declare(strict_types=1);

namespace App\Account\Requests;

use App\Core\BaseRequest;

class RoleRequest extends BaseRequest
{
    public readonly string $name;

    public readonly string $description;

    public readonly array $permission;
}
