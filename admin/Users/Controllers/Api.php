<?php
namespace Admin\Users\Controllers;
use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;

class Api extends AdminController{
	private $error = array();
	private $userModel;
	
	public function __construct(){
		$this->userModel=new UserModel();
    }
	
	public function login(){
		
    }

}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */