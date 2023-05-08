<?php
namespace Admin\Incentive\Controllers;
use Admin\Incentive\Models\IncentiveModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;

class Incentive extends AdminController{
	private $error = array();
	private $incentiveModel;

	public function __construct(){
		$this->incentiveModel=new IncentiveModel();
    }
	
	public function index(){
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		return $this->getList();  
	}
	
	public function add(){
		
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){

			
			return redirect()->to(base_url('admin/incentive'));
		}
		$this->getForm();
	}
	
	public function edit(){
		
		
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			$id=$this->uri->getSegment(4);
            
			$this->incentiveModel->update($id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Incentive Updated Successfully.');
		
			return redirect()->to(base_url('admin/incentive'));
		}
		$this->getForm();
	}
	
	public function delete(){
        helper('filesystem');
		if ($this->request->getPost('selected')){
			$selected = $this->request->getPost('selected');
		}else{
			$selected = (array) $this->uri->getSegment(4);
		}
        foreach($selected as $incentiveid){
		    $incentive=$this->incentiveModel->find($incentiveid);

        }
		$this->incentiveModel->delete($selected);

		$this->session->setFlashdata('message', 'Incentive deleted Successfully.');
		return redirect()->to(base_url('admin/incentive'));
	}
	
	protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);

		$data['add'] = admin_url('incentive/add');
		$data['delete'] = admin_url('incentive/delete');

		$data['heading_title'] = lang('Incentive.heading_title');
		
		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');
		
		$data['button_add'] = lang('Incentive.button_add');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		$this->filterOptions($data);

		return $this->template->view('Admin\Incentive\Views\index', $data);
	}
	
	public function search() {
		$requestData= $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		
		$filter_data = array(

			'filter_search' => $requestData['search']['value'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$totalFiltered = $this->incentiveModel->getTotal($filter_data);
			
		$filteredData = $this->incentiveModel->getAll($filter_data);
		//printr($filteredData);
		$datatable=array();
		foreach($filteredData as $result) {

			$action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('incentive/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('incentive/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->name,
				$result->created_at,
                $result->mstatus?'Incentive Created':'Incentive Uploaded',
				$result->status?'Enable':'Disable',
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
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		

		$data['heading_title'] 	= lang('Incentive.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Incentive Edit" : "Incentive Add";
		$data['cancel'] = admin_url('incentive');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$incentive_info = $this->incentiveModel->find($this->uri->getSegment(4));
		}
		
		foreach($this->incentiveModel->getFieldNames('incentive') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($incentive_info->{$field}) && $incentive_info->{$field}) {
				$data[$field] = html_entity_decode($incentive_info->{$field},ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}

		$data['incentive_types']=[
		    "C"=>"Controller",
            "M"=>"Model",
            "V"=>"View",
            "F"=>"Config",
            "L"=>"Library",
            "O"=>"Other Directory"

        ];

		echo $this->template->view('Admin\Incentive\Views\incentiveForm',$data);
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->incentiveModel->validationRules;

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

	protected function filterOptions(&$data){

        $data['years'] = getAllYears();
        $data['months'] = getAllMonths();
        $data['districts'] = (new DistrictModel())->asArray()->findAll();

        $data['seasons'] = [
            'kharif','rabi'
        ];
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        } else {
            $data['year_id'] = getCurrentYearId();
        }
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        } else {
            $data['month_id'] = getCurrentMonthId();
        }
        if($this->request->getGet('season')){
            $data['season'] = $this->request->getGet('season');
        } else {
            $data['season'] = getCurrentSeason();
        }

        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = 0;
        }

        $data['filter_panel'] = view('Admin\Incentive\Views\filter', $data);
    }

}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */