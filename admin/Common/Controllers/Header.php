<?php

namespace Admin\Common\Controllers;

use App\Controllers\AdminController;

class Header extends AdminController
{
    /**
     * @var \Config\Settings
     */
    protected $settings;

    /**
     * Header constructor.
     */
    public function __construct()
    {
        $this->settings = config('Settings');
    }

    /**
     * @return string
     */
    public function index(): string
    {
        $data = [
            'site_name' => $this->settings->config_site_title,
            'logo' => is_file(DIR_UPLOAD . $this->settings->config_site_logo) ? BASE_URL . 'storage/uploads/' . $this->settings->config_site_logo : '',
            'logout' => admin_url('logout'),
            'relogin' => session('temp_user') !== null,
            'name' => user()->getFirstName(),
        ];

        if (user()->isLogged()) {
            $data['menu'] = menu('admin', ['menu_class' => 'nav-main']);
        }

        return view('Admin\Common\Views\header', $data);
    }
}
