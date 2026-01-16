<?php

declare(strict_types=1);

namespace App\Traits;

trait ProfileCompletionTrait
{
    public function has_incomplete_profile(): bool
    {
        return ! $this->has_photo() || empty($this->phone());
    }
}
