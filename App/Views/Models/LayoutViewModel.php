<?php

declare(strict_types=1);

namespace App\Views\Models;

class LayoutViewModel
{
    private ?UserViewModel $user;

    private ?MenuViewModel $menu;

    public function __construct(?UserViewModel $user, ?MenuViewModel $menu)
    {
        $this->user = $user;
        $this->menu = $menu;
    }

    public function getUser(): ?UserViewModel
    {
        return $this->user;
    }

    public function getMenu(): ?MenuViewModel
    {
        return $this->menu;
    }
}
