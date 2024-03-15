<?php

namespace Admin\Menu\Controllers;

use App\Controllers\AdminController;
use Admin\Menu\Models\MenuModel;
use Admin\Menu\Libraries\Menu;
use App\Libraries\Menu as AppMenu;

class Menu extends AdminController
{
    private $error = [];
    private $menuModel;
    private $menu;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
        $this->menu = new Menu(); // Create an instance
        $this->appMenu = new AppMenu();
    }

    public function index()
    {
        $data = [];
        $this->template->set_meta_title(lang('Menu.heading_title'));
        $this->template->add_package(array('jquerynestable', 'sweetalert'), true);

        $data['breadcrumbs'] = [
            [
                'text' => lang('Menu.heading_title'),
                'href' => admin_url('menu')
            ]
        ];

        $data['menu_groups'] = $menu_groups = $this->menuModel->getMenuGroups();

        $menuGroupId = $this->uri->getSegment(3);
        if (!$menuGroupId) {
            $menuGroupId = $menu_groups ? $menu_groups[0]['id'] : 0;
        }

        $data['menu_group_id'] = $menuGroupId;
        $data['text_form_group'] = $menuGroupId ? 'Save Menu' : 'Create Menu';

        if ($this->request->getMethod(1) === 'POST' && $this->validateMenuForm()) {
            $menugroup_data = [
                "title" => $this->request->getPost('menu_name'),
                "theme_location" => $this->request->getPost('theme_location'),
                "status" => 1
            ];

            if ($menuGroupId) {
                //update menugroup
                $this->menuModel->editMenuGroup($menuGroupId, $menugroup_data);
            } else {
              
