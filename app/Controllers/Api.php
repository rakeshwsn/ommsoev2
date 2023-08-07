<?php
namespace App\Controllers;

use Admin\Dashboard\Models\AreaCoverageModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Api extends ResourceController
{
	use ResponseTrait;
	private $user; 
	
	public function __construct(){
		helper("aio");
		$this->user=service('user');
	}
	
	public function index(){
		$data['message'] = "Welcome to OMM";
		return $this->respond($data);
	}
	
}
