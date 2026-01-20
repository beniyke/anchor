<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Auth\Views\Models\ResetPasswordViewModel;
use App\Core\BaseController;
use App\Services\UserService;
use App\Validations\Form\ResetPasswordFormRequestValidation;
use Helpers\Http\Response;

class ResetpasswordController extends BaseController
{
    public function index(?string $token = null): Response
    {
        $resetpassword_view_model = $this->container->make(ResetPasswordViewModel::class, compact('token'));

        if (! $resetpassword_view_model->resetTokenIsValid()) {
            $this->flash->error('Password reset link has expired.');

            return $this->resposne->redirect($this->request->fullRouteByName('forgot-password'));
        }

        return $this->asView('reset-password', compact('resetpassword_view_model'));
    }

    public function store(ResetPasswordFormRequestValidation $validator, UserService $service, ?string $token = null): Response
    {
        $user = $service->getUserByValidResetToken($token);

        if (! $this->request->isPut() || ! $user) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->post();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->withInput($formdata, $validator->errors());

            return $this->response->redirect($this->request->callback());
        }

        $password_changed = $service->setNewUserPassword($user, $validator->getRequest());

        if (! $password_changed) {
            $this->flash->error('Password reset failed.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('Password reset successful. Log in to continue.');

        return $this->response->redirect($this->request->fullRouteByName('login'));
    }
}
