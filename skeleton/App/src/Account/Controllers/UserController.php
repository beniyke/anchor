<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Services\AccountService;
use App\Account\Views\Models\CreateUserViewModel;
use App\Account\Views\Models\EditUserViewModel;
use App\Account\Views\Models\RoleViewModel;
use App\Account\Views\Models\UserListViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;
use Permit\Models\Role;

class UserController extends BaseController
{
    public function index(AccountService $service): Response
    {
        if ($this->request->filled('resend')) {
            $this->handleResendingActivationToken($service);

            return $this->response->redirect($this->request->fullRoute());
        }

        $page = $this->request->get('page', 1);
        $users = $service->listUsers($this->request->validated(), $page);
        $user_list_view_model = new UserListViewModel($this->container, $users);

        return $this->asView('user.list', compact('user_list_view_model'));
    }

    public function create(): Response
    {
        $roles = Role::all();
        $role_view_model = RoleViewModel::collection($roles->all());
        $create_user_view_model = new CreateUserViewModel($this->container, $role_view_model);

        return $this->asView('user.create', compact('create_user_view_model'));
    }

    public function edit(AccountService $service, ?string $refid = null): Response
    {
        $user = $service->getUser($refid);

        if (! $user) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $authUser = $this->auth->user();

        if ($user->hasActivationToken() || $user->isSelf($authUser)) {
            if ($authUser->id === $user->id) {
                $this->flash->error('You do not have the permission to edit your account.');
            }

            return $this->response->redirect($this->request->fullRoute());
        }

        $roles = Role::all();
        $role_view_model = RoleViewModel::collection($roles->all());
        $edit_user_view_model = new EditUserViewModel($this->container, $user, $role_view_model);

        return $this->asView('user.edit', compact('edit_user_view_model'));
    }

    private function handleResendingActivationToken(AccountService $service): void
    {
        if (! $service->resendActivationToken($this->request->get('resend'))) {
            $this->flash->error('Resending link failed.');

            return;
        }

        $this->flash->success('Activation link resent.');
    }

    public function store(AccountService $service): Response
    {
        if (! $this->request->isPut()) {
            return $this->response->redirect($this->request->callback());
        }

        $registered = $service->preRegisterUser($this->request->validated());

        if (! $registered) {
            $this->flash->error('User could not be created.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('User successfully created.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function update(AccountService $service, ?string $refid = null): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->callback());
        }

        $user = $service->getUser($refid);

        if (! $user) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $updated = $service->updateUser($user, $this->request->validated());

        if (! $updated) {
            $this->flash->error('User could not be updated.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('User successfully updated.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function destroy(AccountService $service, ?string $refid = null): Response
    {
        if (! $this->request->isDelete()) {
            return $this->response->redirect($this->request->callback());
        }

        $user_deleted = $service->deleteUser($refid);

        if (! $user_deleted) {
            $this->flash->error('User could not be deleted.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('User successfully deleted.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
