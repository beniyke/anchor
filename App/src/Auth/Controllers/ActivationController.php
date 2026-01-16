<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Core\BaseController;
use App\Services\UserService;
use Helpers\Http\Response;

class ActivationController extends BaseController
{
    public function index(UserService $service, ?string $activation_token = null): Response
    {
        if (empty($activation_token)) {
            $this->flash->error('Invalid or missing activation token.');

            return $this->response->redirect($this->request->fullRouteByName('login'));
        }

        if (! $service->activateUser($activation_token)) {
            $this->flash->error('Account activation failed.');

            return $this->response->redirect($this->request->fullRouteByName('login'));
        }

        $this->flash->success('Account successfully activated. You can now log in.');

        return $this->response->redirect($this->request->fullRouteByName('login'));
    }
}
