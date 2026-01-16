<?php

declare(strict_types=1);

namespace App\Auth\Views\Models;

use App\Services\UserService;
use Helpers\Http\Flash;

readonly class LoginViewModel
{
    public string $page_title;

    public string $heading;

    public string $subheading;

    public function __construct(
        private readonly UserService $service,
        private readonly Flash $flash
    ) {
        $this->page_title = 'Login';
        $this->heading = 'Login';
        $this->subheading = 'Sign in to your account to continue.';
    }

    public function hasSetup(): bool
    {
        return ! $this->service->isFirstUserSetup();
    }

    public function getErrorClass(string $field): string
    {
        return $this->flash->hasInputError($field) ? 'is-invalid' : '';
    }

    public function hasError(string $field): bool
    {
        return $this->flash->hasInputError($field);
    }

    public function getFormActionUrl(): string
    {
        return url(route('attempt'));
    }

    public function getSignupUrl(): string
    {
        return url('signup');
    }

    public function getForgotPasswordUrl(): string
    {
        return url('auth/recoverpassword');
    }
}
