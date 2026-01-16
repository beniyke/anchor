<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Auth\Views\Models\LoginViewModel;
use App\Core\BaseController;
use App\Validations\Form\LoginFormRequestValidation;
use Helpers\Http\Response;

class LoginController extends BaseController
{
    public function index(LoginViewModel $login_view_model): Response
    {
        return $this->asView('login', compact('login_view_model'));
    }

    public function attempt(LoginFormRequestValidation $validator): Response
    {
        if (! $this->request->isPost()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $validator->validate($this->request->post());

        if ($validator->has_error()) {
            $this->flash->error($validator->errors());

            return $this->response->redirect($this->request->fullRoute());
        }

        if (! $this->auth->login($validator->getRequest())) {
            return $this->response->redirect($this->request->fullRoute());
        }

        return $this->handleLoginRedirect();
    }

    private function handleLoginRedirect(): Response
    {
        $redirect_to = $this->request->fullRouteByName('home');

        if (! empty($callback_url) && ! $this->request->isLoginRoute()) {
            $redirect_to = $this->request->callback();
        }

        return $this->response->redirect($redirect_to);
    }
}
