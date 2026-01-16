<?php

declare(strict_types=1);

namespace App\Traits;

trait TokenExpirationTrait
{
    private const TOKEN_EXPIRATION_DURATION = 48;

    public function reset_token_has_expired(): bool
    {
        return ! $this->has_reset_token() || $this->has_reset_token() && $this->token_expired($this->_reset_token_date());
    }

    public function activation_token_has_expired(): bool
    {
        return $this->has_activation_token() && $this->token_expired($this->_activation_token_date());
    }

    public function reset_token_expiration(): string
    {
        return $this->has_reset_token() ? datetime($this->_reset_token_date())
            ->addHours(self::TOKEN_EXPIRATION_DURATION)
            ->format('D, d M Y H:i A') : '';
    }

    private function token_expired(?string $token_date): bool
    {
        return datetime($token_date)->diffInHours(datetime()->format('Y-m-d H:i:s')) > self::TOKEN_EXPIRATION_DURATION;
    }
}
