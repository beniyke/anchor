<?php

declare(strict_types=1);

namespace App\Auth\Views\Models;

use App\Auth\Services\IdentityService;
use App\Models\User;
use Helpers\Http\Flash;

readonly class ResetPasswordViewModel
{
    public string $page_title;

    public string $heading;

    public string $subheading;

    private string $token;

    private IdentityService $service;

    private Flash $flash;

    public function __construct(IdentityService $service, Flash $flash, ?string $token = null)
    {
        $this->flash = $flash;
        $this->token = $token;
        $this->service = $service;
        $this->page_title = 'Reset Password';
        $this->heading = 'Reset Password';
        $this->subheading = "Hi {$this->getValidUser()?->name}, set a new, secure password for your account.";
    }

    public function getValidUser(): ?User
    {
        return $this->service->getUserByValidResetToken($this->token);
    }

    public function resetTokenIsValid(): bool
    {
        return ! empty($this->getValidUser());
    }

    public function getFormActionUrl(): string
    {
        return url(route('store/' . $this->token));
    }

    public function getErrorClass(string $field): string
    {
        return $this->flash->hasInputError($field) ? 'is-invalid' : '';
    }

    public function hasError(string $field): bool
    {
        return $this->flash->hasInputError($field);
    }
}
