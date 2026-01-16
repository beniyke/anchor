<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseAction
{
    abstract public function execute(mixed $data): mixed;
}
