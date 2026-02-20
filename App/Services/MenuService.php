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

        return $this->getFilteredMenu($menu, $user);
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
                            ->push($urls)
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
     * Filters the menu based on the user's role and permissions.
     */
    private function getFilteredMenu(array $menu, User $user): array
    {
        $filteredMenu = [];
        foreach ($menu as $mainMenuItem) {
            if ($user->can($mainMenuItem['permission'])) {
                if (! empty($mainMenuItem['submenu'])) {
                    $mainMenuItem['submenu'] = $this->filterSubmenu($mainMenuItem, $user);
                }

                if (empty($mainMenuItem['submenu']) || count($mainMenuItem['submenu']) > 0) {
                    $filteredMenu[] = $mainMenuItem;
                }
            }
        }

        return $filteredMenu;
    }

    /**
     * Filters submenu items based on user permissions.
     */
    private function filterSubmenu(array $menu, User $user): array
    {
        return array_filter($menu['submenu'], function ($item) use ($menu, $user) {
            return $user->can($item['permission']);
        });
    }
}
