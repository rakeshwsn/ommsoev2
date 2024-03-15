<?php

namespace Admin\Common\Controllers;

use App\Controllers\AdminController;
use App\Services\UserService;

class Leftbar extends AdminController
{
    protected $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = service('user');
    }

    public function index()
    {
        $data = [
            'menus' => []
        ];

        $this->addMenu('dashboard', 'Dashboard', 'md-home', admin_url('/'), []);
        $this->addMenu('pages', 'Pages', 'md-pages', '', $this->getPageItems());
        $this->addMenu('forms', 'Forms', 'md-forms', '', $this->getFormItems());
        $this->addMenu('banner', 'Banner', 'md-account-child', '', $this->getBannerItems());
        $this->addMenu('menu', 'Menu', 'md-menu', admin_url('menu'), []);
        $this->addMenu('proceeding', 'Proceeding', 'md-proceeding', admin_url('proceeding'), []);
        $this->addMenu('localisations', 'Localisations', 'md-localisations', '', $this->getLocalisationItems());
        $this->addMenu('user', 'Users', 'md-users', '', $this->getUserItems());
        $this->addMenu('system', 'System', 'md-settings', '', $this->getSystemItems());

        return view('Admin\Common\Views\leftbar', $data);
    }

    protected function addMenu($id, $name, $icon, $href, $children)
    {
        $data['menus'][] = [
            'id' => $id,
            'icon' => $icon,
            'name' => lang("Leftbar.text_{$id}"),
            'href' => $href,
            'children' => $children
        ];
    }

    protected function getPageItems()
    {
        $pages = [];

        if ($this->userService->hasPermission('access', 'pages/index')) {
            $pages[] = $this->createMenuItem('list_page', 'pages');
        }

        if ($this->userService->hasPermission('access', 'pages/add')) {
            $pages[] = $this->createMenuItem('add_page', 'pages/add');
        }

        return $pages;
    }

    protected function getFormItems()
    {
        $forms = [];

        $methods = ['household', 'aggriculture', 'horticulture', 'livestock', 'fishery', 'institution'];

        foreach ($methods as $method) {
            if ($this->userService->hasPermission("access", "{$method}/index")) {
                $forms[] = $this->createMenuItem($method, $method);
            }
        }

        return $forms;
    }

    protected function getBannerItems()
    {
        $banner = [];

        if ($this->userService->hasPermission('access', 'banner/index')) {
            $banner[] = $this->createMenuItem('all_banner', 'banner');
        }

        if ($this->userService->hasPermission('access', 'banner/add')) {
            $banner[] = $this->createMenuItem('add_banner', 'banner/add');
        }

        return $banner;
    }

    protected function getLocalisationItems()
    {
        $localisation = [];

        $methods = ['district', 'block', 'grampanchayat', 'cluster', 'village'];

        foreach ($methods as $method) {
            if ($this->userService->hasPermission("access", "{$method}/index")) {
                $localisation[] = $this->createMenuItem($method, $method);
            }
        }

        return $localisation;
    }

    protected function getUserItems()
    {
        $user = [];

        $methods = ['user', 'users/members', 'users/usergroup', 'permission'];

        foreach ($methods as $method) {
            if ($this->userService->hasPermission("access", $method)) {
                $user[] = $this->createMenuItem($method, $method);
            }
        }

        return $user;
    }

    protected function getSystemItems()
    {
        $system = [];

        $methods = ['setting', 'setting/serverinfo'];

        foreach ($methods as $method) {
            if ($this->userService->hasPermission("access", $method)) {
                $system[] = $this->createMenuItem($method, $method);
            }
        }

        return $system;
    }

    protected function createMenuItem($method, $href)
    {
        return [
            'name' => lang("Leftbar.text_{$method}"),
            'href' => admin_url("{$method}"),
            'children' => []
        ];
    }
}
