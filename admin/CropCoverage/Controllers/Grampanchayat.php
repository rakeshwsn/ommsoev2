<?php
namespace Admin\CropCoverage\Controllers;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\GrampanchayatModel;
use Admin\CropCoverage\Models\DistrictModel;
use Admin\CropCoverage\Models\VillageModel;

class Grampanchayat extends AdminController{
	private $error = array();
	private $grampanchayatModel;
	
	public function __construct(){
		$this->grampanchayatModel=new GrampanchayatModel();
	}
	
	public function index(){
		$this->template->set_meta_title(lang('Grampanchayat.heading_title'));
		return $this->getList();  
	}
	
	public function add(){
		
		$this->template->set_meta_title(lang('Grampanchayat.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			
			$id=$this->grampanchayatModel->insert($this->request->getPost());
			
			$this->session->setFlashdata('message', 'Grampanchayat Saved Successfully.');
			
			return redirect()->to(base_url('admin/areacoverage/gp'));
		}
		$this->getForm();
	}
	
	public function edit(){
		
		$this->template->set_meta_title(lang('Grampanchayat.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			$id=$this->uri->getSegment(4);
			
			$this->grampanchayatModel->update($id,$this->request->getPost());
			
			$this->session->setFlashdata('message', 'Grampanchayat Updated Successfully.');
		
			return redirect()->to(base_url('admin/areacoverage/gp'));
		}
		$this->getForm();
	}
	
	public function delete(){
		if ($this->request->getPost('selected')){
			$selected = $this->request->getPost('selected');
		}else{
			$selected = (array) $this->uri->getSegment(4);
		}
		$this->grampanchayatModel->delete($selected);
		
		$this->session->setFlashdata('message', 'Grampanchayat deleted Successfully.');
		return redirect()->to(base_url('admin/areacoverage/gp'));
	}
	
	protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);
		
		$this->template->add_package(array('datatable','select2'),true);

		$data['add'] = admin_url('cropcoverage/gp/add');
		$data['delete'] = admin_url('grampanchayat/delete');
		$data['datatable_url'] = admin_url('grampanchayat/search');

		$data['heading_title'] = lang('Grampanchayat');
		
		$data['text_list'] = lang('Grampanchayat.text_list');
		$data['text_no_results'] = lang('Grampanchayat.text_no_results');
		$data['text_confirm'] = lang('Grampanchayat.text_confirm');
		
		$data['button_add'] = lang('Grampanchayat.button_add');
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
		
		$districtModel=new DistrictModel();
		$data['districts'] = $districtModel->getAll();
		// print_r($data);
		// exit;
		
	

		return $this->template->view('Admin\CropCoverage\Views\grampanchayat', $data);
	}
	
	public function search() {
		$requestData= $_REQUEST;
		$totalData = $this->grampanchayatModel->getTotals();
		$totalFiltered = $totalData;
		
		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'filter_district' => $requestData['district'],
            'filter_block' => $requestData['block'],
			'filter_grampanchayat' => $requestData['grampanchayat'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$totalFiltered = $this->grampanchayatModel->getTotals($filter_data);
			
		$filteredData = $this->grampanchayatModel->getAll($filter_data);

		$datatable=array();
		foreach($filteredData as $result) {

			$action  = '<div class="btn-group btn-group-sm pull-right">';
			$action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('grampanchayat/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('grampanchayat/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->name,
				$result->district,
                $result->block,
				$action
			);
	
		}
		//printr($datatable);
		$json_data = array(
			"draw"            => isset($requestData['draw']) ? intval( $requestData['draw'] ):1,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $datatable
		);
		
		return $this->response->setContentType('application/json')
								->setJSON($json_data);
		
	}
	
	protected function getForm(){
		
		$this->template->add_package(array('select2'),true);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);
		
		//printr($_SESSION);
		$_SESSION['isLoggedIn'] = true;
        
		$data['heading_title'] 	= lang('Grampanchayat.heading_title');
		$data['text_form'] = $this->uri->getSegment(3) ? "Grampanchayat Edit" : "Grampanchayat Add";
		$data['cancel'] = admin_url('grampanchayat');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$grampanchayat_info = $this->grampanchayatModel->find($this->uri->getSegment(4));
		}
		
		foreach($this->grampanchayatModel->getFieldNames('grampanchayat') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($grampanchayat_info->{$field}) && $grampanchayat_info->{$field}) {
				$data[$field] = html_entity_decode($grampanchayat_info->{$field},ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}
		
		$districtModel=new DistrictModel();
		$data['districts'] = $districtModel->getAll();
		// echo "<pre>";
		//  print_r($data['districts']);
		//  exit;
		
	
		echo $this->template->view('Admin\CropCoverage\Views\grampanchayatForm',$data);
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->grampanchayatModel->validationRules;
		
		
		
		if ($this->validate($rules)){
			return true;
    	}
		else{
			//printr($validation->getErrors());
			$this->error['warning']="Warning: Please check the form carefully for errors!";
			return false;
    	}
		return !$this->error;
	}

    public function village($gp=''){
        if (is_ajax()){
            $villageModel=new VillageModel();
            if(!is_numeric($gp)){
                $gprow=$this->grampanchayatModel->where('code', $gp)->first();

                $gp=$gprow->id;
            }
            $json = array(
                'grampanchayat'  	=> $gp,
                'village'   => $villageModel->getVillageByGP($gp)
            );
            echo json_encode($json);
        }else{
            return show_404();
        }
    }
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */
?>