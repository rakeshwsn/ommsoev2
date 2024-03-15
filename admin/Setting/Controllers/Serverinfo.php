<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Serverinfo extends BaseController
{
    private $error = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Load the language file
        $this->lang->load('serverinfo');

        // Set the meta title and add the CSS file
        $this->template->set_meta_title($this->lang->line('heading_title'));
        $this->template->add_stylesheet('modules/setting/assets/css/serverinfo.css');

        // Set the breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->lang->line('text_settings'),
            'href' => base_url('setting')
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->lang->line('heading_title'),
            'href' => base_url('setting/serverinfo')
        ];

        // Set the page heading
        $data['heading_title'] = $this->lang->line('heading_title');

        // Get the phpinfo() output
        $data['phpinfo'] = $this->getPhpInfo();

        // Render the view
        if ($this->template->view('serverinfo', $data)) {
            return true;
        }

        // Display an error message if the view failed to render
        show_error('Failed to render the server info view.');
    }

    private function getPhpInfo(): string
    {
        ob_start();
        phpinfo();
        $pinfo = ob_get_contents();
        ob_end_clean();

        // Improved regular expression to extract the body of the phpinfo() output
        return preg_replace('/^.*<body([^>]*)>(.*)<\/body>.*$/s', '$2', $pinfo);
    }

    public function server_info()
    {
        // Load the language file
        $this->lang->load('serverinfo');

        // Set the breadcrumb
        $data['
