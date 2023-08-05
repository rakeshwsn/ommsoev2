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
	
	public function districtArea(){
		$data=(new AreaCoverageModel())->getAll();
		return $this->respond($data);
	}
	
	public function login(){
		
		
	}
	
	public function xform(){
		
	}
	
	public function dashboarddata(){
		
		
	}
	
	public function formdata(){
	   
	}
	
	protected function fmd5($url=''){
		
	}
	
	public function itemset(){
        
	}
	
	private function generateGVItemset($filter){
		
		
	}
	
	private function generateGVFItemset($filter){
		
	}

    private function generateATPItemset($filter){
        

    }

    
    private function generateGVSItemset($filter){
       

    }
	public function farmer(){
       
    }
	
	public function code(){
	    
    }
	
	protected function validateLoginForm() {
		
	}

    protected function encodeFunc($value) {
       
    }

    protected function arraysearch($array,$search){
        foreach ($array as $element){
            if(strpos($element,$search)!==FALSE){
                return TRUE;
            }
        }
        return FALSE;
    }
}
