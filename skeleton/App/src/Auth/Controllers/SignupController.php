<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Auth\Services\IdentityService;
use App\Auth\Views\Models\SignupViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class SignupController extends BaseController
{
    public function index(SignupViewModel $signup_view_model): Response
    {
        if ($signup_view_model->hasSetup()) {
            return $this->response->redirect($this->request->fullRouteByName('login'));
        }

        return $this->asView('signup', compact('signup_view_model'));
    }

    public function store(IdentityService $service): Response
    {
        $has_setup = $service->isFirstUserSetup();

        if (! $this->request->isPost() || ! $has_setup) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $user_registered = $service->registerUser($this->request->validated());

        if (! $user_registered) {
            $this->flash->withInput($this->request->post(), 'Signup failed.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('Signup successful. An activation link has been sent to your mail box.');

        return $this->response->redirect($this->request->fullRouteByName('login'));
    }
}
