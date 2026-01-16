<?php

declare(strict_types=1);

namespace App\Auth\Views\Models;

use App\Services\UserService;
use Helpers\Http\Flash;

readonly class SignupViewModel
{
    public string $page_title;

    public string $heading;

    public string $subheading;

    private readonly UserService $service;

    private readonly Flash $flash;

    public function __construct(UserService $service, Flash $flash)
    {
        $this->page_title = 'Create Account';
        $this->heading = 'Create Account';
        $this->subheading = 'Provide the following required information to create your account.';
        $this->service = $service;
        $this->flash = $flash;
    }

    public function hasSetup(): bool
    {
        return ! $this->service->isFirstUserSetup();
    }

    public function getFormActionUrl(): string
    {
        return url(route('store'));
    }

    public function getLoginUrl(): string
    {
        return url('login');
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
