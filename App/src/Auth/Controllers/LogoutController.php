<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Core\BaseController;
use Helpers\Http\Response;

class LogoutController extends BaseController
{
    public function index(): Response
    {
        $user = $this->auth->user();
        $user_id = $user->id;

        if (! $this->auth->logout()) {
            return $this->response->redirect($this->request->fullRouteByName('home'));
        }

        activity('logged out.', user_id: $user_id);

        return $this->response->redirect($this->request->fullRouteByName('login'));
    }
}
