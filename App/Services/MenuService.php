<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\Array\Collections;

class MenuService
{
    protected readonly ConfigServiceInterface $config;

    public function __construct(ConfigServiceInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Retrieves the menu for a given user based on their permissions.
     */
    public function getMenuForUser(User $user): array
    {
        $menu = $this->config->get('app.menu');

        if ($user->isSuperAdmin()) {
            return $this->getAllowedMenuForSuperAdmin($menu);
        }

        $permissions = $user->role->permission;
        $type = $user->role->type->value;

        return $this->getFilteredMenu($menu, $permissions, $type);
    }

    /**
     * Gets all routes accessible to the user.
     */
    public function getAccessibleRoutes(User $user): array
    {
        $menu = $this->getMenuForUser($user);

        $urls = Collections::make($menu)
            ->pluck('url')
            ->custom(function ($array) use ($menu) {
                $submenus = Collections::make($menu)
                    ->pluck('submenu')
                    ->clean()
                    ->custom(function ($submenuArray) {
                        $data = [];
                        foreach ($submenuArray as $values) {
                            $data = array_merge($data, $values);
                        }

                        return $data;
                    })
                    ->pluck('url')
                    ->custom(function ($urls) use ($menu) {
                        return Collections::make($menu)
                            ->pluck('submenu')
                            ->clean()
                            ->custom(function ($submenu_array) {
                                $data = [];
                                foreach ($submenu_array as $values) {
                                    $data = array_merge($data, $values);
                                }

                                return $data;
                            })
                            ->pluck('routes')
                            ->clean()
                            ->flatten()
                            ->attach($urls)
                            ->get();
                    });

                $menu_routes = Collections::make($menu)->pluck('routes')->flatten()->clean()->get();

                return $submenus
                    ->push($menu_routes)
                    ->push(array_filter($array))
                    ->push($this->config->get('route.custom'))
                    ->get();
            })->clean(function (string $value) {
                return strpos($value, '#') === false;
            })->rebase()->unique();

        return $urls->get();
    }

    /**
     * Retrieves a flat list of all menu and submenu items for a super admin.
     */
    private function getAllowedMenuForSuperAdmin(array $menu): array
    {
        return $menu;
    }

    /**
     * Filters the menu based on the user's role and permissions.
     */
    private function getFilteredMenu(array $menu, array $permissions, string $type): array
    {
        $filteredMenu = [];
        foreach ($menu as $mainMenuItem) {
            if (in_array($type, $mainMenuItem['type'])) {
                $menuPermissionKey = str_replace('/', '-', $mainMenuItem['url']);
                if (in_array($menuPermissionKey, $permissions['menu'])) {
                    $mainMenuItemHasSubmenu = ! empty($mainMenuItem['submenu']);

                    if ($mainMenuItemHasSubmenu) {
                        $mainMenuItem['submenu'] = $this->filterSubmenu($mainMenuItem, $permissions['submenu'], $type);
                    }

                    if (! $mainMenuItemHasSubmenu || count($mainMenuItem['submenu']) > 0) {
                        $filteredMenu[] = $mainMenuItem;
                    }
                }
            }
        }

        return $filteredMenu;
    }

    /**
     * Filters submenu items based on user permissions.
     */
    private function filterSubmenu(array $menu, array $submenuPermissions, string $type): array
    {
        return array_filter($menu['submenu'], function ($item) use ($submenuPermissions, $menu, $type) {
            $permissionKey = str_replace('/', '-', $menu['url'].'::'.$item['url']);

            return in_array($type, $item['type']) && in_array($permissionKey, $submenuPermissions);
        });
    }
}
