<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Services\RoleService;
use App\Account\Views\Models\CreateRoleViewModel;
use App\Account\Views\Models\EditRoleViewModel;
use App\Account\Views\Models\RoleListViewModel;
use App\Account\Views\Models\RoleViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class RoleController extends BaseController
{
    public function index(RoleService $service): Response
    {
        $page = $this->request->get('page', 1);
        $roles = $service->listRoles($page);
        $role_list_view_model = new RoleListViewModel($this->container, $roles);

        return $this->asView('role.list', compact('role_list_view_model'));
    }

    public function create(RoleService $service): Response
    {
        $create_role_view_model = new CreateRoleViewModel($this->container, $service);

        return $this->asView('role.create', compact('create_role_view_model'));
    }

    public function edit(RoleService $service, ?string $slug = null): Response
    {
        $role = $service->getRole($slug);

        if (! $role) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $role_view_model = new RoleViewModel($role);
        $edit_role_view_model = new EditRoleViewModel($this->container, $role_view_model);

        return $this->asView('role.edit', compact('edit_role_view_model'));
    }

    public function store(RoleService $service): Response
    {
        if (!$this->request->isPut()) {
            return $this->response->redirect($this->request->callback());
        }

        $role_created = $service->createRole($this->request->validated());

        if (! $role_created) {
            $this->flash->error('Role could not be created.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('Role successfully created with permissions synced.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function update(RoleService $service, ?string $slug = null): Response
    {
        if (!$this->request->isPostOrPatch()) {
            return $this->response->redirect($this->request->callback());
        }

        $role = $service->getRole($slug);

        if (! $role) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $updated = $service->updateRole($role, $this->request->validated());

        if (! $updated) {
            $this->flash->error('Role could not be updated.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('Role successfully updated.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function destroy(RoleService $service, ?string $refid = null): Response
    {
        if (! $this->request->isDelete()) {
            return $this->response->redirect($this->request->callback());
        }

        $role = $service->getRole($refid);

        if (! $role) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $deleted = $service->deleteRole($role);

        if (! $deleted) {
            $this->flash->error('Role could not be deleted.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('Role successfully deleted.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
