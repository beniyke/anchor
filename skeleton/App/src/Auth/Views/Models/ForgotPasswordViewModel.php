<?php

declare(strict_types=1);

namespace App\Auth\Views\Models;

readonly class ForgotPasswordViewModel
{
    public string $page_title;

    public string $heading;

    public string $subheading;

    public function __construct()
    {
        $this->page_title = 'Recover Password';
        $this->heading = 'Recover Password';
        $this->subheading = 'Please enter the email address associated with your account to reset your password.';
    }

    public function getFormActionUrl(): string
    {
        return url(route('attempt'));
    }

    public function getLoginUrl(): string
    {
        return url('login');
    }
}
