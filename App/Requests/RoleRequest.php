<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class RoleRequest extends BaseRequest
{
    public readonly string $type;

    public readonly string $title;

    public readonly array $permission;
}
