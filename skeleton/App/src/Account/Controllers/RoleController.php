<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Validations\Form\RoleFormRequestValidation;
use App\Account\Views\Models\CreateRoleViewModel;
use App\Account\Views\Models\EditRoleViewModel;
use App\Account\Views\Models\RoleListViewModel;
use App\Core\BaseController;
use App\Services\RoleService;
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

    public function edit(RoleService $service, ?string $refid = null): Response
    {
        $role = $service->getRole($refid);

        if (! $role) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $edit_role_view_model = new EditRoleViewModel($this->container, $role);

        return $this->asView('role.edit', compact('edit_role_view_model'));
    }

    public function store(RoleFormRequestValidation $validator, RoleService $service): Response
    {
        if (! $this->request->isPut()) {
            return $this->response->redirect($this->request->callback());
        }

        $formdata = $this->request->post();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->error($validator->errors());

            return $this->response->redirect($this->request->callback());
        }

        $role_created = $service->createRole($validator->getRequest());

        if (! $role_created) {
            $this->flash->error('Role could not be created.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('Role successfully created.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function update(RoleFormRequestValidation $validator, RoleService $service, ?string $refid = null): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->callback());
        }

        $role = $service->getRole($refid);

        if (! $role) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->post();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->error($validator->errors());

            return $this->response->redirect($this->request->callback());
        }

        $updated = $service->updateRole($role, $validator->getRequest());

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
