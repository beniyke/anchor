<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Auth\Services\IdentityService;
use App\Auth\Views\Models\ForgotPasswordViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class RecoverpasswordController extends BaseController
{
    public function index(ForgotPasswordViewModel $forgotpassword_view_model): Response
    {
        return $this->asView('recover-password', compact('forgotpassword_view_model'));
    }

    public function attempt(IdentityService $service): Response
    {
        if (! $this->request->isPost()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $password_reset = $service->resetUserPassword($this->request->validated());

        if (! $password_reset) {
            $this->flash->success('If your email is associated with an active account, you will receive password reset instructions.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('A password reset link has been sent to your mail.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
