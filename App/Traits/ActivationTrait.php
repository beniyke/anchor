<?php

declare(strict_types=1);

namespace App\Traits;

trait ActivationTrait
{
    public function activation_token(): ?string
    {
        return $this->getColumn('activation_token');
    }

    private function _activation_token_date(): ?string
    {
        $date = $this->getColumn('activation_token_created_at');

        return $this->formatDate($date);
    }

    public function has_activation_token(): bool
    {
        return ! empty($this->activation_token()) && ! empty($this->_activation_token_date());
    }
}
