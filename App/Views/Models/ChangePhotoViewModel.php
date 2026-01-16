<?php

declare(strict_types=1);

namespace App\Views\Models;

use Core\Ioc\ContainerInterface;
use Helpers\Http\Flash;
use Helpers\Http\Request;

readonly class ChangePhotoViewModel
{
    protected UserViewModel $user_view_model;

    protected Flash $flash;

    protected Request $request;

    public function __construct(ContainerInterface $container, UserViewModel $user_view_model)
    {
        $this->flash = $container->get(Flash::class);
        $this->request = $container->get(Request::class);
        $this->user_view_model = $user_view_model;
    }

    public function getPageTitle(): string
    {
        return 'Change Photo';
    }

    public function getHeading(): string
    {
        return 'Setting';
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('update');
    }

    public function hasPhoto(): bool
    {
        return $this->user_view_model->hasPhoto();
    }

    public function getAvatarUrl(): string
    {
        return $this->user_view_model->getAvatar();
    }

    public function getUserName(): string
    {
        return $this->user_view_model->getName();
    }

    public function hasError(string $field): bool
    {
        return $this->flash->hasInputError($field);
    }
}
