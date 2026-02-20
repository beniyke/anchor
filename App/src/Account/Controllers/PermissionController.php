<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Services\AccountService;
use App\Account\Views\Models\UserPermissionViewModel;
use App\Core\BaseController;
use App\Views\Models\UserViewModel;
use Helpers\Http\Response;

class PermissionController extends BaseController
{
    public function index(AccountService $service, ?string $refid = null): Response
    {
        $user = $service->getUser($refid);

        if (!$user) {
            return $this->response->redirect($this->request->fullRoute('user', true));
        }

        $user_view_model = new UserViewModel($user);
        $user_permission_view_model = new UserPermissionViewModel($this->container, $user_view_model);

        return $this->asView('role.permission', compact('user_permission_view_model'));
    }

    public function update(AccountService $service, ?string $refid = null): Response
    {
        if (!$this->request->isPatch()) {
            return $this->response->redirect($this->request->callback());
        }

        $user = $service->getUser($refid);

        if (!$user) {
            return $this->response->redirect($this->request->fullRoute('user', true));
        }

        $updated = $service->updateUserPermissions($user, $this->request->validated());

        if (!$updated) {
            $this->flash->error('Permissions could not be updated.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('User permissions successfully updated.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
