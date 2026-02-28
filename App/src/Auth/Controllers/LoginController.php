<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Auth\Views\Models\LoginViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class LoginController extends BaseController
{
    public function index(LoginViewModel $login_view_model): Response
    {
        return $this->asView('login', compact('login_view_model'));
    }

    public function attempt(): Response
    {
        if (! $this->request->isPost()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $login = $this->auth->login($this->request->validated());

        if (! $login->isSuccessful()) {
            $this->flash->error($login->getMessage() ?? 'Invalid login credentials.');

            return $this->response->redirect($this->request->fullRoute());
        }

        return $this->handleLoginRedirect();
    }

    private function handleLoginRedirect(): Response
    {
        $redirect_to = $this->request->fullRouteByName('home');

        if (! $this->request->isLoginRoute()) {
            $redirect_to = $this->request->callback();
        }

        return $this->response->redirect($redirect_to);
    }
}
