<?php

namespace App\Controllers\Admin\Component;

use App\Controllers\BaseController;

class Controller extends BaseController
{
    protected $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function index()
    {
        // Set default data
        $this->data['title'] = 'Dashboard';

        // Load view
        return view('admin/component/index', $this->data);
    }

    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function getData($key = null)
    {
        if ($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }

        return $this->data;
    }
}

