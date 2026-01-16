<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use App\Account\Views\Models\Traits\UserViewModelTrait;
use Core\Ioc\ContainerInterface;
use Helpers\Http\Request;

readonly class CreateUserViewModel
{
    use UserViewModelTrait;

    private array $roles;

    private Request $request;

    public function __construct(ContainerInterface $container, array $roles)
    {
        $this->request = $container->get(Request::class);
        $this->roles = $roles;
    }

    public function getPageTitle(): string
    {
        return 'Create User';
    }

    public function getHeading(): string
    {
        return 'Create User';
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute();
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('store');
    }

    public function getImportButtonLabel(): string
    {
        return '<span class="fas fa-cloud-upload fa-fw"></span> Import User';
    }

    public function getImportButtonAttributes(): array
    {
        return [
            'class' => 'btn btn-success btn-lg',
            'data-bs-toggle' => 'modal',
        ];
    }

    public function getImportModalId(): string
    {
        return '#import-user';
    }
}
