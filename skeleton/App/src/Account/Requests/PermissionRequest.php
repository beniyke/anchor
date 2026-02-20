<?php

declare(strict_types=1);

namespace App\Account\Requests;

use App\Core\BaseRequest;

class PermissionRequest extends BaseRequest
{
    public readonly array $permissions;
}
