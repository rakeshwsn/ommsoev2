<?php
namespace Admin\Common\Controllers;
use App\Controllers\AdminController;

class Errors extends AdminController {

	function __construct(){

	}
	public function index(){
	
        $data = [];
        return $this->template->view('Admin\Common\Views\error',$data);
	}
	
}
/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */