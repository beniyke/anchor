<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Services\AccountService;
use App\Account\Views\Models\ChangePasswordViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class ChangepasswordController extends BaseController
{
    public function index(): Response
    {
        $change_password_view_model = new ChangePasswordViewModel($this->container, $this->user_view_model);

        return $this->asView('profile.change-password', compact('change_password_view_model'));
    }

    public function update(AccountService $service): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $password_changed = $service->changeUserPassword($this->auth->user(), $this->request->validated());

        if (! $password_changed) {
            $this->flash->error('Password change failed.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('Password successfully changed.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
