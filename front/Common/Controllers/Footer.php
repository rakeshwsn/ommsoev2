<?php

namespace Front\Common\Controllers;

use App\Controllers\BaseController;
use Config\Services;

class Footer extends BaseController
{
    public function index()
    {
        // Return the footer view
        return Services::renderer()->view('Front\Common\Views\footer', [], true);
    }
}
