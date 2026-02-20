<?php

declare(strict_types=1);

namespace App\Account\Requests;

use App\Core\BaseRequest;

class SearchUserRequest extends BaseRequest
{
    public readonly ?string $search;

    public readonly ?string $status;
}
