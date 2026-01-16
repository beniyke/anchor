<?php

declare(strict_types=1);

namespace App\Website\Controllers;

use App\Core\BaseController;
use Helpers\Http\Response;

class HomeController extends BaseController
{
    public function index(): Response
    {
        return $this->asView('landing');
    }
}
