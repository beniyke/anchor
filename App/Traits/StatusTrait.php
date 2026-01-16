<?php

declare(strict_types=1);

namespace App\Traits;

trait StatusTrait
{
    public function status(): string
    {
        return $this->getColumn('status');
    }

    public function has_active_account(): bool
    {
        return $this->status() === 'active';
    }

    public function color(): string
    {
        return [
            'inactive' => 'danger',
            'active' => 'success',
        ][$this->status()] ?? 'default';
    }
}
