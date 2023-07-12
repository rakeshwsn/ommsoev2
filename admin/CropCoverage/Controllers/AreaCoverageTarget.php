<?php
namespace Admin\CropCoverage\Controllers;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;

class AreaCoverageTarget extends AdminController {
    private $error = array();
	private $targetModel;
	private $blockModel;
	private $districtModel;
	private $cropsModel;
	private $practicesModel;
    
    function __construct(){
        $this->targetModel= new TargetModel();
		$this->blockModel = new BlockModel();
		$this->districtModel = new DistrictModel();
		$this->cropsModel = new CropsModel;
		$this->practicesModel = new PracticesModel;
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

		if ($this->request->getGet('district_id')) {
			$data['district_id'] = (array)$this->request->getGet('district_id');
		} else {
			$data['district_id'] = $this->user->district_id;
		}

        $croppractices = $this->targetModel->getPractices();
		
		$practicedata=   $this->targetModel->getAll([
			'district_id'=>$data['district_id']
		]);
		
		$data['practicedata'] = $practicedata;
		$data['year_id'] = date('Y');
        $currentMonth = date('n');
        if ($currentMonth >= 6 && $currentMonth <= 10) {
            $season = 'Kharif';
        } elseif ($currentMonth >= 11 && $currentMonth <= 4) {
            $season = 'Rabi';
        }
		$data['season'] = $season; 
		// printr($practicedata);
		// exit;

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
		
		return $this->template->view('Admin\CropCoverage\Views\areacoveragetarget', $data);
	}
    public function add(){
		
		if ($this->request->getMethod(1) === 'POST'){
           
			$id=$this->targetModel->AddTargets($this->request->getPost());
           
			$this->session->setFlashdata('message', 'Target Saved Successfully.');
			
			return redirect()->to(base_url('admin//areacoverage/target'));
		}
		$this->getForm();
	}
	
    protected function getForm(){
		$_SESSION['isLoggedIn'] = true;
		
		$data['cancel'] = admin_url('areacoverage/target');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
        $data['crops'] = $this->cropsModel->GetCrops();
		$data['practics'] = $this->practicesModel->GetPractices();
        $data['district_id']=$this->user->district_id;
		if (empty($this->user->district_id)) {
			$data['missingDistrictId'] = true;
		} else {
			$data['missingDistrictId'] = false;
			$data['district_id'] = $this->user->district_id;
		}
		$data['districts'] = $this->districtModel->getAll();

		// dd($data['districts']);

		$data['blocks']=(new BlockModel())->getBlocksByDistrict($data['district_id']);
        $data['block_id']=$this->user->block_id;
        
		echo $this->template->view('Admin\CropCoverage\Views\targetForm',$data);
	}
	public function fetchBlocks()
    {
        if ($this->request->isAJAX()) {
            $districtId = $this->request->getPost('districtId');
            
            // Fetch blocks from the BlockModel based on district ID
      
            $blocks = $blockModel->where('district_id', $districtId)->findAll();

            // Prepare options array for the block dropdown
            $blockOptions = [];
            foreach ($blocks as $block) {
                $blockOptions[$block->id] = $block->name;
            }

            // Return blocks as JSON response
            return $this->response->setJSON(['success' => true, 'blocks' => $blockOptions]);
        } else {
            // Invalid request
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
    }
}
?>
