<?php

declare(strict_types=1);

namespace App\Core\Data;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\MenuService;
use App\Views\Models\LayoutViewModel;
use App\Views\Models\MenuViewModel;
use App\Views\Models\UserViewModel;
use Helpers\Http\Request;

class ViewData
{
    private LayoutViewModel $layout;

    private ?UserViewModel $user = null;

    public function __construct(Request $request, AuthServiceInterface $authService, MenuService $menuService)
    {
        $fullUserViewModel = null;
        $menuViewModel = null;
        $userViewModel = null;

        if ($authService->isAuthenticated() && ! $request->routeIsApi()) {
            $user = $authService->user();
            $menu = $menuService->getMenuForUser($user);
            $fullUserViewModel = UserViewModel::full($user);
            $menuViewModel = new MenuViewModel($menu, route());
            $userViewModel = UserViewModel::basic($user);
        }

        $this->layout = new LayoutViewModel($fullUserViewModel, $menuViewModel);
        $this->user = $userViewModel;
    }

    public function layout(): LayoutViewModel
    {
        return $this->layout;
    }

    public function user(): ?UserViewModel
    {
        return $this->user;
    }
}
