<?php
namespace Admin\CropCoverage\Controllers;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\CropCoverage\Models\BlockModel;



class AreaCoverageTarget extends AdminController {

    private $error = array();

    function __construct(){
         $this->targetModel=new TargetModel();
    }
    public function Index(){

        $this->template->set_meta_title(lang('Grampanchayat.heading_title'));
		return $this->getList(); 
    }
    protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);
		
		$this->template->add_package(array('datatable','select2'),true);

		$data['add'] = admin_url('areacoverage/target/add');
		$data['delete'] = admin_url('grampanchayat/delete');
		$data['datatable_url'] = admin_url('grampanchayat/search');

		$data['heading_title'] = lang('Area Coverage Target');
		
		$data['text_list'] = lang('Grampanchayat.text_list');
		$data['text_no_results'] = lang('Grampanchayat.text_no_results');
		$data['text_confirm'] = lang('Grampanchayat.text_confirm');
		
		$data['button_add'] = lang('Target.button_add');
		$data['button_edit'] = lang('Grampanchayat.button_edit');
		$data['button_delete'] = lang('Grampanchayat.button_delete');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array)$this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}
        $targetmodel = new TargetModel();
        $croppractices = $targetmodel->getPractices();

        //for heading
        $crops = [];
        foreach($croppractices as $cp){
            $_crops = $cp['crops'];

            if (!isset($crops[$_crops])) {
                $crops[$_crops] = array();
            }
        
            $crops[$_crops][] = $cp['practice'];
        }
        
        $data['heading'] = $crops;
        


		// $districtModel=new DistrictModel();
		// $data['districts'] = $districtModel->getAll();
		// print_r($data);
		// exit;
		
	

		return $this->template->view('Admin\CropCoverage\Views\areacoveragetarget', $data);
	}
    public function add(){
		
		
		if ($this->request->getMethod(1) === 'POST'){
           
           // printr	($this->request->getPost());
           // exit;
			$id=$this->targetModel->AddTargets($this->request->getPost());
           
			
			$this->session->setFlashdata('message', 'Target Saved Successfully.');
			
			return redirect()->to(base_url('admin//areacoverage/target'));
		}
		$this->getForm();
	}
    protected function getForm(){
		
		$this->template->add_package(array('select2'),true);
		
		$_SESSION['isLoggedIn'] = true;
		
		$data['cancel'] = admin_url('areacoverage/target');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
         $cropsModel=new CropsModel();
		 $data['crops'] = $cropsModel->GetCrops();
        //  print_r($data);
		//  exit;
         $practicesModel= new practicesModel();
		 $data['practics'] = $practicesModel->GetPractices();
         $data['district_id']=$this->user->district_id;
		$data['blocks']=(new BlockModel())->getBlocksByDistrict($data['district_id']);
       $data['block_id']=$this->user->block_id;
        //   echo "<pre>";
		//   print_r($data['blocks']);
		//   exit;
		echo $this->template->view('Admin\CropCoverage\Views\targetForm',$data);
	}
   
   
}
?>
