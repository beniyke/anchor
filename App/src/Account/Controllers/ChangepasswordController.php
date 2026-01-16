<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Core\BaseController;
use App\Services\UserService;
use App\Validations\Form\ChangePasswordFormRequestValidation;
use App\Views\Models\ChangePasswordViewModel;
use Helpers\Http\Response;

class ChangepasswordController extends BaseController
{
    public function index(): Response
    {
        $change_password_view_model = new ChangePasswordViewModel($this->container, $this->user_view_model);

        return $this->asView('profile.change-password', compact('change_password_view_model'));
    }

    public function update(ChangePasswordFormRequestValidation $validator, UserService $service): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->post();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->withInput($formdata, $validator->errors());

            return $this->response->redirect($this->request->fullRoute());
        }

        $password_changed = $service->changeUserPassword($this->auth->user(), $validator->getRequest());

        if (! $password_changed) {
            $this->flash->error('Password change failed.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('Password successfully changed.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
