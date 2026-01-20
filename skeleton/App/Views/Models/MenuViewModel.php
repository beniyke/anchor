<?php

declare(strict_types=1);

namespace App\Views\Models;

class MenuViewModel
{
    private array $items;

    public function __construct(array $menuItems, string $currentRoute)
    {
        $this->items = $this->transformMenuItems($menuItems, $currentRoute);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    private function transformMenuItems(array $menuItems, string $currentRoute): array
    {
        $transformed = [];
        foreach ($menuItems as $item) {
            $hasSubmenu = ! empty($item['submenu']);
            $isActive = $this->isMenuItemActive($item, $currentRoute);
            $isDropdownOpen = $hasSubmenu && $this->isDropdownActive($item, $currentRoute);

            $transformedItem = [
                'title' => $item['title'],
                'url' => $item['url'],
                'icon' => $item['icon'] ?? '',
                'has_submenu' => $hasSubmenu,
                'is_active' => $isActive,
                'is_dropdown_open' => $isDropdownOpen,
                'submenu' => [],
            ];

            if ($hasSubmenu) {
                foreach ($item['submenu'] as $sub) {
                    $transformedItem['submenu'][] = [
                        'title' => $sub['title'],
                        'url' => $sub['url'],
                        'is_active' => $this->isMenuItemActive($sub, $currentRoute),
                    ];
                }
            }

            $transformed[] = $transformedItem;
        }

        return $transformed;
    }

    private function isMenuItemActive(array $item, string $currentRoute): bool
    {
        if ($item['url'] === $currentRoute) {
            return true;
        }

        if (isset($item['routes']) && is_array($item['routes']) && in_array($currentRoute, $item['routes'])) {
            return true;
        }

        return false;
    }

    private function isDropdownActive(array $item, string $currentRoute): bool
    {
        if (empty($item['submenu'])) {
            return false;
        }

        foreach ($item['submenu'] as $sub) {
            if ($sub['url'] === $currentRoute) {
                return true;
            }
            if (isset($sub['routes']) && is_array($sub['routes']) && in_array($currentRoute, $sub['routes'])) {
                return true;
            }
        }

        return false;
    }
}
