<?php

declare(strict_types=1);

/**
 * This abstract class serves as a foundational controller for the application,
 * providing common functionality for handling HTTP requests and responses.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Core;

use App\Core\Data\ViewData;
use App\Core\Traits\ResponseFormatterTrait;
use App\Core\Traits\ViewHandlerTrait;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Views\Models\LayoutViewModel;
use App\Views\Models\UserViewModel;
use Core\Ioc\ContainerInterface;
use Helpers\Http\Flash;
use Helpers\Http\Request;
use Helpers\Http\Response;

abstract class BaseController
{
    use ResponseFormatterTrait;
    use ViewHandlerTrait;

    protected Request $request;

    protected Response $response;

    protected Flash $flash;

    protected AuthServiceInterface $auth;

    protected ContainerInterface $container;

    protected ViewData $viewData;

    protected LayoutViewModel $layout_view_model;

    protected ?UserViewModel $user_view_model;

    public function __construct(ContainerInterface $container, Request $request, Response $response, AuthServiceInterface $authService, Flash $flash, ViewData $viewData)
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
        $this->auth = $authService;
        $this->flash = $flash;
        $this->viewData = $viewData;
        $this->layout_view_model = $viewData->layout();
        $this->user_view_model = $viewData->user();
    }
}
