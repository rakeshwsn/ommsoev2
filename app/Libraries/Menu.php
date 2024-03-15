<?php

declare(strict_types=1);

namespace App\Libraries;

use Admin\Menu\Models\MenuModel;

class Menu
{
    private $user;

    public function __construct()
    {
        $this->user = service('user');
    }

    public function nav_menu(array $data): string
    {
        $menu_model = new MenuModel();
        $menu = $menu_model->getMenus($data);

        if ($data['theme_location'] === "admin") {
            $menus = $this->getNestedMenus($menu, 'admin');
            $nav = $this->create_admin_nav($menus, $data);
        } else {
            $menus = $this->getNestedMenus($menu);
            $nav = $this->create_nav($menus, $data);
        }

        return $nav;
    }

    private function getNestedMenus(array $elements, string $parentId = '0', string $type = 'frontend'): array
    {
        $tree = [];
        $sessionPermission = ($type === 'admin') ? $this->user->getPermissions() : [];

        foreach ($elements as $element) {
            if (
                ($element->url === '#' || ($type === 'admin' && isset($sessionPermission[$element->url]) && $sessionPermission[$element->url] !== "no"))
                && (string)$element->parent_id === (string)$parentId
            ) {
                $sub = $this->getNestedMenus($elements, $element->id, $type);
                if ($sub) {
                    $element->sub = $sub;
                }
                $tree[] = $element;
            }
        }

        return $tree;
    }

    // ... rest of the code
}
