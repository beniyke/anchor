<?php

declare(strict_types=1);

namespace App\Views\Models;

use Core\Ioc\ContainerInterface;
use Helpers\Http\Flash;
use Helpers\Http\Request;

readonly class ProfileViewModel
{
    protected UserViewModel $user_view_model;

    protected Flash $flash;

    protected Request $request;

    public function __construct(ContainerInterface $container, UserViewModel $user_view_model)
    {
        $this->user_view_model = $user_view_model;
        $this->flash = $container->get(Flash::class);
        $this->request = $container->get(Request::class);
    }

    public function getPageTitle(): string
    {
        return 'Profile';
    }

    public function getHeading(): string
    {
        return 'Setting';
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('update');
    }

    public function getFieldValue(string $field): string
    {
        $oldValue = $this->flash->old($field);

        if ($oldValue !== null) {
            return $oldValue;
        }

        return match ($field) {
            'name' => $this->user_view_model->getName(),
            'email' => $this->user_view_model->getEmail(),
            'phone' => $this->user_view_model->getPhone() ?? '',
            'gender' => $this->user_view_model->getGender(),
            default => '',
        };
    }

    public function getGenders(): array
    {
        return ['male' => 'Male', 'female' => 'Female'];
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
