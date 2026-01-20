<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Auth\Views\Models\SignupViewModel;
use App\Core\BaseController;
use App\Services\UserService;
use App\Validations\Form\SignupFormRequestValidation;
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

    public function store(SignupFormRequestValidation $validator, UserService $service): Response
    {
        $has_setup = $service->isFirstUserSetup();

        if (! $this->request->isPost() || ! $has_setup) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->post();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->withInput($formdata, $validator->errors());

            return $this->response->redirect($this->request->fullRoute());
        }

        $user_registered = $service->registerUser($validator->getRequest());

        if (! $user_registered) {
            $this->flash->withInput($formdata, 'Signup failed.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('Signup successful. An activation link has been sent to your mail box.');

        return $this->response->redirect($this->request->fullRouteByName('login'));
    }
}
