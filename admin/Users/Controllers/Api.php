<?php

namespace Admin\Users\Controllers;

use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;

class Api extends AdminController
{
    protected $error = [];
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // Add your login logic here
    }

    // Add other methods as needed
}

/* End of file Api.php */
/* Location: ./application/modules/Admin/Users/Controllers/Api.php */
