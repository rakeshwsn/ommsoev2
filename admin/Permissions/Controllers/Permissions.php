<?php
namespace Admin\Permissions\Controllers;
use App\Controllers\AdminController;
use Admin\Permissions\Models\PermissionsModel;
use Admin\Users\Models\UserGroupModel;

class Permissions extends AdminController{
	private $error = array();
	private $permissionsModel;

	public function __construct(){
		$this->permissionsModel=new PermissionsModel();
    }
	
	public function index(){
		$this->template->set_meta_title(lang('Permissions.heading_title'));
		return $this->getList();  
	}
	
	public function add(){
		
		$this->template->set_meta_title(lang('Permissions.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			//printr($this->request->getPost());
			//exit;
			$this->permissionsModel->insert($this->request->getPost());
            $this->session->setFlashdata('message', 'Permissions Saved Successfully.');
			
			return redirect()->to(base_url('admin/permissions'));
		}
		$this->getForm();
	}
	
	public function edit(){
		
		
		$this->template->set_meta_title(lang('Permissions.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			$id=$this->uri->getSegment(4);
            
			$this->permissionsModel->update($id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Permissions Updated Successfully.');
		
			return redirect()->to(base_url('admin/permissions'));
		}
		$this->getForm();
	}
	
	public function delete(){
		if ($this->request->getPost('selected')){
			$selected = $this->request->getPost('selected');
		}else{
			$selected = (array) $this->uri->getSegment(4);
		}
		$this->permissionsModel->delete($selected);
		$this->session->setFlashdata('message', 'Permissions deleted Successfully.');
		return redirect()->to(base_url('admin/permissions'));
	}
	
	protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Permissions.heading_title'),
			'href' => admin_url('permissions')
		);
		
		$this->template->add_package(array('datatable'),true);

		$data['add'] = admin_url('permissions/add');
		$data['delete'] = admin_url('permissions/delete');
		$data['datatable_url'] = admin_url('permissions/search');

		$data['heading_title'] = lang('Permissions.heading_title');
		
		$data['text_list'] = lang('Permissions.text_list');
		$data['text_no_results'] = lang('Permissions.text_no_results');
		$data['text_confirm'] = lang('Permissions.text_confirm');
		
		$data['button_add'] = lang('Permissions.button_add');
		$data['button_edit'] = lang('Permissions.button_edit');
		$data['button_delete'] = lang('Permissions.button_delete');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array)$this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}

		return $this->template->view('Admin\Permissions\Views\permissions', $data);
	}
	
	public function search() {
		$requestData= $_REQUEST;
		$totalData = $this->permissionsModel->getTotal();
		$totalFiltered = $totalData;
		
		$filter_data = array(

			'filter_search' => $requestData['search']['value'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$totalFiltered = $this->permissionsModel->getTotal($filter_data);
			
		$filteredData = $this->permissionsModel->getAll($filter_data);
		//printr($filteredData);
		$datatable=array();
		foreach($filteredData as $result) {

			$action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('permissions/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('permissions/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->route,
				$result->module,
				$result->description,
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
		
		$this->template->add_package(array('select2','flatpickr','ckfinder'),true);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Permissions.heading_title'),
			'href' => admin_url('permissions')
		);
		

		$data['heading_title'] 	= lang('Permissions.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Permissions Edit" : "Permissions Add";
		$data['cancel'] = admin_url('permissions');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$permissions_info = $this->permissionsModel->find($this->uri->getSegment(4));
		}
		
		foreach($this->permissionsModel->getFieldNames('permissions') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($permissions_info->{$field}) && $permissions_info->{$field}) {
				$data[$field] = html_entity_decode($permissions_info->{$field},ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}

		$data['permission_actions']=[
			'index'=>'Index',
			'add'=>'Add',
			'edit'=>'Edit',
			'delete'=>'Delete',
			'view'=>'View',
			'other'=>'Other'
		];

		echo $this->template->view('Admin\Permissions\Views\permissionsForm',$data);
	}

	public function assign(){
		$id = $this->uri->getSegment(4);
        $data['user_group_id']=$id;
		$usergroupmodel= new UserGroupModel();

        if ($this->request->getMethod(1) === 'POST'){

            $this->permissionsModel->addUserGroupPermission($id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Permission Updated Successfully.');

            return redirect()->to(admin_url('usergroup'));
        }
        if((int)$id) {
            $usergroup_info = $usergroupmodel->find($id);
            $data['text_form'] = $usergroup_info->name ." Permission";
            $data['cancel'] = admin_url('usergroup');
            $data['id']=$id;
            $gpermissions= (array)$this->permissionsModel->get_modules_with_permissions($id);
            $data['gpermissions']=[];
			
            foreach($gpermissions as $gpermission){
				$module=$gpermission->module?:'Other';
				$action=$gpermission->action?:'mis';
				$data['gpermissions'][$module][$action][]=$gpermission;
			}
			//printr( $data['gpermissions']);
			//exit;
            echo $this->template->view('Admin\Permissions\Views\assignPermissions',$data);
        }else {

        }
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->permissionsModel->validationRules;

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

}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */