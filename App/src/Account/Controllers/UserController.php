<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Validations\Form\SearchUserFormRequestValidation;
use App\Account\Validations\Form\UserFormRequestValidation;
use App\Account\Views\Models\CreateUserViewModel;
use App\Account\Views\Models\EditUserViewModel;
use App\Account\Views\Models\UserListViewModel;
use App\Core\BaseController;
use App\Models\Role;
use App\Services\UserService;
use App\Views\Models\RoleViewModel;
use Helpers\Http\Response;

class UserController extends BaseController
{
    public function index(SearchUserFormRequestValidation $validator, UserService $service): Response
    {
        if ($this->request->filled('resend')) {
            $this->handleResendingActivationToken($service);

            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->get();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->error($validator->errors());

            return $this->response->redirect($this->request->fullRoute());
        }

        $page = $this->request->get('page', 1);
        $users = $service->listUsers($validator->getRequest(), $page);
        $user_list_view_model = new UserListViewModel($this->container, $users);

        return $this->asView('user.list', compact('user_list_view_model'));
    }

    public function create(): Response
    {
        $roles = Role::allCached();
        $role_view_model = array_map(fn ($role) => new RoleViewModel($role), $roles);
        $create_user_view_model = new CreateUserViewModel($this->container, $role_view_model);

        return $this->asView('user.create', compact('create_user_view_model'));
    }

    public function edit(UserService $service, ?string $refid = null): Response
    {
        $user = $service->getUser($refid);

        if (! $user) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $authUser = $this->auth->user();

        if ($user->hasActivationToken() || ($authUser->id === $user->id)) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $roles = Role::allCached();
        $role_view_model = RoleViewModel::collection($roles);
        $edit_user_view_model = new EditUserViewModel($this->container, $user, $role_view_model);

        return $this->asView('user.edit', compact('edit_user_view_model'));
    }

    private function handleResendingActivationToken(UserService $service): void
    {
        if (! $service->resendActivationToken($this->request->get('resend'))) {
            $this->flash->error('Resending link failed.');

            return;
        }

        $this->flash->success('Activation link resent.');

    }

    public function store(UserFormRequestValidation $validator, UserService $service): Response
    {
        if (! $this->request->isPut()) {
            return $this->response->redirect($this->request->callback());
        }

        $formdata = $this->request->post();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->withInput($formdata, $validator->errors());

            return $this->response->redirect($this->request->callback());
        }

        $payload = $validator->validated();
        $registered = $service->preRegisterUser($validator->getRequest());

        if (! $registered) {
            $this->flash->error('User could not be created.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('User successfully created.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function update(UserFormRequestValidation $validator, UserService $service, ?string $refid = null): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->callback());
        }

        $user = $service->getUser($refid);

        if (! $user) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->post();
        $formdata['id'] = $user->id;
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->withInput($formdata, $validator->errors());

            return $this->response->redirect($this->request->callback());
        }

        $updated = $service->updateUser($user, $validator->getRequest());

        if (! $updated) {
            $this->flash->error('User could not be updated.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('User successfully updated.');

        return $this->response->redirect($this->request->fullRoute());
    }

    public function destroy(UserService $service, ?string $refid = null): Response
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

    public function import(): Response
    {
        if (! $this->request->exist('put')) {
            return redirect($this->request->callback());
        }

        $formdata = $this->request->all();
        $validator = (new ImportUserFormRequestValidation($formdata))->validate();

        if ($validator->has_error()) {
            flash()->error($validator->errors());

            return redirect($this->request->callback());
        }

        $payload = $validator->validated();
        $user_imported = (new ImportUserFromExcelAction())->execute($payload);

        if (! $user_imported) {
            flash()->error('Users could not be imported.');

            return redirect($this->request->callback());
        }

        defer(function () {
            activity('imported users');
        });

        flash()->success('Users successfully imported.');

        return redirect(route());
    }
}
