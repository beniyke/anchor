<?php

declare(strict_types=1);

namespace App\Views\Models;

use Core\Ioc\ContainerInterface;
use Helpers\Http\Flash;
use Helpers\Http\Request;

readonly class ChangePasswordViewModel
{
    protected UserViewModel $userViewModel;

    protected Flash $flash;

    protected Request $request;

    public function __construct(ContainerInterface $container, UserViewModel $userViewModel)
    {
        $this->flash = $container->get(Flash::class);
        $this->request = $container->get(Request::class);
        $this->userViewModel = $userViewModel;
    }

    public function getPageTitle(): string
    {
        return 'Change password';
    }

    public function getHeading(): string
    {
        return 'Setting';
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('update');
    }

    public function shouldShowPasswordUpdateWarning(): bool
    {
        return $this->userViewModel->shouldUpdatePassword();
    }

    public function hasError(string $field): bool
    {
        return $this->flash->hasInputError($field);
    }

    public function getErrorClass(string $field): string
    {
        return $this->hasError($field) ? 'is-invalid' : '';
    }
}
