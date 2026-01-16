<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use App\Account\Views\Models\Traits\UserViewModelTrait;
use App\Models\User;
use Core\Ioc\ContainerInterface;
use Helpers\Http\Request;

readonly class EditUserViewModel
{
    use UserViewModelTrait;

    private User $user;

    private array $roles;

    private Request $request;

    public function __construct(ContainerInterface $container, User $user, array $roles)
    {
        $this->request = $container->get(Request::class);
        $this->user = $user;
        $this->roles = $roles;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getName(): string
    {
        return $this->user->name ?? '';
    }

    public function getEmail(): string
    {
        return $this->user->email ?? '';
    }

    public function getRoleId(): ?int
    {
        return $this->user->role_id;
    }

    public function getStatus(): string
    {
        return $this->user->status->value;
    }

    public function getGender(): string
    {
        return $this->user->gender;
    }

    public function getPageTitle(): string
    {
        return 'Edit User';
    }

    public function getHeading(): string
    {
        return 'Edit User';
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute();
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('update'.'/'.$this->user->refid);
    }
}
