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

    public function navMenu(array $data): string
    {
        $menuModel = new MenuModel();
        $menu = $menuModel->getMenus($data);

        $menus = $this->getNestedMenus($menu, $data['theme_location']);

        return $this->createNav($menus, $data);
    }

    private function getNestedMenus(array $elements, string $parentId = '0', string $type = 'frontend'): array
    {
        $tree = [];
        $sessionPermission = ($type === 'admin') ? $this->user->getPermissions() : [];

        foreach ($elements as $element) {
            $canAccess = ($element->url === '#' || ($type === 'admin' && isset($sessionPermission[$element->url]) && $sessionPermission[$element->url] !== "no"));

            if ($canAccess && (string)$element->parent_id === (string)$parentId) {
                $subMenus = $this->getNestedMenus($elements, $element->id, $type);

                if (!empty($subMenus)) {
                    $element->sub = $subMenus;
                }

                $tree[] = $element;
            }
        }

        return $tree;
    }

    private function createNav(array $menus, array $data): string
    {
        // Implement the creation of the navigation menu here
        // ...

        return $nav;
    }

    // ... rest of the code
}
