<?php

declare(strict_types=1);

namespace App\Requests;

use App\Core\BaseRequest;

class SearchUserRequest extends BaseRequest
{
    public readonly ?string $search;

    public readonly ?string $status;
}
