<?php

declare(strict_types=1);

namespace App\Auth\Controllers;

use App\Core\BaseController;
use Helpers\Http\Response;

class LogoutController extends BaseController
{
    public function index(): Response
    {
        $this->auth->logout();

        return $this->response->redirect($this->request->fullRouteByName('login'));
    }
}
