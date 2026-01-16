<?php

declare(strict_types=1);

namespace App\Traits;

trait PasswordTrait
{
    private const PASSWORD_UPDATE_DURATION = 30;

    public function password(): string
    {
        return $this->getColumn('password');
    }

    public function password_updated_at(): ?string
    {
        $date = $this->getColumn('password_updated_at');

        return $this->formatDate($date);
    }

    public function should_update_password(): bool
    {
        $password_last_updated = $this->password_updated_at();

        if (empty($password_last_updated)) {
            return true;
        }

        return datetime($password_last_updated)->diffInDays(datetime()->format('Y-m-d H:i:s')) > self::PASSWORD_UPDATE_DURATION;
    }

    public function is_new_user(): bool
    {
        return empty($this->password_updated_at());
    }

    public function reset_token(): ?string
    {
        return $this->getColumn('reset_token');
    }

    private function _reset_token_date(): ?string
    {
        $token_created_date = $this->getColumn('reset_token_created_at');

        return $this->formatDate($token_created_date);
    }

    public function has_reset_token(): bool
    {
        return ! empty($this->reset_token()) && ! empty($this->_reset_token_date());
    }
}
