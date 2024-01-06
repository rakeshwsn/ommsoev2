<?php
namespace Admin\Equipment\Controllers;
use App\Controllers\AdminController;

use Admin\Equipment\Models\EquipmentModel;
use CodeIgniter\API\ResponseTrait;

class Equipment extends AdminController{
	private $error = array();
	private $equipmentmodel;

	use ResponseTrait;
	
	public function __construct(){
		$this->equipmentmodel =new EquipmentModel();
	}
	
	public function index(){
		$this->template->set_meta_title(lang('Equipment List'));
		return $this->getList();  

	}
	
	public function add(){
		
		$this->template->set_meta_title(lang('Equipment List'));
		// dd($this->request->getPost());
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	

			$id=$this->equipmentmodel->insert($this->request->getPost());
			
			$this->session->setFlashdata('message', 'Equipment Saved Successfully.');
			
			return redirect()->to(base_url('admin/equipment'));
		}
		
		$this->getForm();
	}
	
	public function edit(){
		
		$this->template->set_meta_title(lang('Equipment List'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			$id=$this->uri->getSegment(4);
			

			$this->equipmentmodel->update($id,$this->request->getPost());
			
			$this->session->setFlashdata('message', 'Equipment Updated Successfully.');
		
			return redirect()->to(base_url('admin/equipment'));
		}
		$this->getForm();
	}
	
	public function delete(){
		if ($this->request->getPost('selected')){
			$selected = $this->request->getPost('selected');
		}else{
			$selected = (array) $this->uri->getSegment(4);
		}
		$this->equipmentmodel->delete($selected);
		
		$this->session->setFlashdata('message', 'Equipment deleted Successfully.');
		return redirect()->to(base_url('admin/equipment'));
	}
	
	protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Equipment List'),
			'href' => admin_url('equipment')
		);
		
		$this->template->add_package(array('datatable'),true);

		$data['add'] = admin_url('equipment/add');
		$data['delete'] = admin_url('equipment/delete');
		$data['datatable_url'] = admin_url('equipment/search');

		$data['heading_title'] = lang('Equipment List');
		
		$data['text_list'] = lang('Equipment.text_list');
		$data['text_no_results'] = lang('Equipment.text_no_results');
		$data['text_confirm'] = lang('Equipment.text_confirm');
		
		$data['button_add'] = lang('Equipment.button_add');
		$data['button_edit'] = lang('Equipment.button_edit');
		$data['button_delete'] = lang('Equipment.button_delete');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array)$this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}
		return $this->template->view('Admin\Equipment\Views\equipment', $data);

	}
	
	public function search() {
		$requestData= $_REQUEST;
		$totalData = $this->equipmentmodel->getTotals();
		$totalFiltered = $totalData;
		
		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'center_type' => $requestData['center_type'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$totalFiltered = $this->equipmentmodel->getTotals($filter_data);
			
		$filteredData = $this->equipmentmodel->getAll($filter_data);
		
		$datatable=array();
		foreach($filteredData as $result) {

			$action  = '<div class="btn-group btn-group-sm pull-right">';
			$action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('equipment/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('equipment/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->name,
				$result->center_type,
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
		
		$this->template->add_package(array('colorbox'),true);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Equipment List'),
			'href' => admin_url('equipment')
		);
		
		//printr($_SESSION);
		$_SESSION['isLoggedIn'] = true;
        
		$data['heading_title'] 	= lang('Equipment List');
		$data['text_form'] = $this->uri->getSegment(4) ? "Equipment Edit" : "Equipment Add";
		$data['cancel'] = admin_url('equipment');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$equipment_info = $this->equipmentmodel->find($this->uri->getSegment(4));
		}
		
		foreach($this->equipmentmodel->getFieldNames('equipment') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($equipment_info->{$field}) && $equipment_info->{$field}) {
				$data[$field] = html_entity_decode($equipment_info->{$field},ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}
	// dd($data);
		echo $this->template->view('Admin\Equipment\Views\equipmentForm',$data);
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->equipmentmodel->validationRules;
		//dd($validation->getErrors());
		if ($this->validate($rules)){
			return true;
    	}
		else{
			//printr($validation->getErrors());
			$this->error['warning']="Warning: Please check the form carefully for errors!";
			return false;
    	}
		//return !$this->error;
	}
	
	
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */