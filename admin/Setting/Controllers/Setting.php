<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use Admin\Banner\Models\BannerModel;
use Admin\Pages\Models\PagesModel;
use Admin\Setting\Models\SettingModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use Config\Services;

class Setting extends AdminController
{
    private SettingModel $settingModel;
    private UserModel $userModel;
    private array $settings;
    private array $data;
    private array $errors;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->userModel = new UserModel();
        $this->settings = Services::settings();
        $this->data = [];
        $this->errors = [];
    }

    public function index(): void
    {
        // Set the page title
        $this->template->setMetaTitle(lang('Setting.heading_title'));

        // Add necessary packages
        $this->template->addPackage(['ckeditor', 'colorbox', 'select2'], true);

        // Set the page data
        $this->data['heading_title'] = lang('Setting.heading_title');
        $this->data['button_save'] = lang('Setting.button_save');
        $this->data['button_cancel'] = lang('Setting.button_cancel');

        // Set the breadcrumbs
        $this->data['breadcrumbs'] = [
            [
                'text' => lang('banner.heading_title'),
                'href' => adminUrl('banner')
            ]
        ];

        // Check if the form is submitted
        if ($this->request->getMethod(1) === 'post' && $this->validateSetting()) {

            // Save the settings
            $this->settingModel->editSetting('config', $this->request->getPost());

            // Set the flash message
            $this->session->setFlashdata('message', 'Settings saved');

            // Redirect to the current page
            redirect()->to(currentUrl());
        }

        // Set the form action and cancel URL
        $this->data['action'] = adminUrl('setting');
        $this->data['cancel'] = adminUrl('setting');

        // Set the error message
        if (!empty($this->errors['warning'])) {
            $this->data['error'] = $this->errors['warning'];
        }

        // Get the user info
        if ($this->request->getMethod(1) !== 'post') {
            $userInfo = $this->userModel->find(1);
        }

        // Set the default values for the settings
        $settingKeys = $this->settingModel->where('module', 'config')->findAll();
        foreach ($settingKeys as $key => $setting) {
            $field = $setting->key;
            $value = $setting->value;

            if ($this->request->getPost($field)) {
                $this->data[$field] = $this->request->getPost($field);
            } elseif (!empty($this->settings->{$field})) {
                $this->data[$field] = $this->settings->{$field};
            } else {
                $this->data[$field] = '';
            }
        }

        // Set the logo and icon images
        if ($this->request->getPost('config_site_logo') && is_file(DIR_UPLOAD . $this->request->getPost('config_site_logo'))) {
            $this->data['thumb_logo'] = resize($this->request->getPost('config_site_logo'), 100, 100);
        } elseif (!empty($this->settings->config_site_logo) && is_file(DIR_UPLOAD . $this->settings->config_site_logo)) {
            $this->data['thumb_logo'] = resize($this->settings->config_site_logo, 100, 100);
        } else {
            $this->data['thumb_logo'] = resize('no_image.png', 100, 100);
        }

        if ($this->request->getPost('config_site_icon') && is_file(DIR_UPLOAD . $this->request->getPost('config_site_icon'))) {
            $this->data['thumb_icon'] = resize($this->request->getPost('config_site_icon'), 100, 100);
        } elseif (!empty($this->settings->config_site_icon) && is_file(DIR_UPLOAD . $this->settings->config_site_icon)) {
            $this->data['thumb_icon'] = resize($this->settings->config_site_icon, 100, 100);
        } else {
            $this->data['thumb_icon'] = resize('no_image.png', 100, 100);
        }

        // Set the no image image
        $this->data['no_image'] = resize('no_image.png', 100, 1
