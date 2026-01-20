<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    public readonly string $old_password;

    public readonly string $new_password;
}
