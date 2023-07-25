<?php
namespace Admin\Letters\Controllers;
use App\Controllers\AdminController;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Letters\Models\LetteruserModel;

class Users extends AdminController{
	private $error = array();
	private $userModel;
	private $slugModel;
	
	public function __construct(){
		$this->letteruserModel=new LetteruserModel();
    }
	
	public function index(){
		$this->template->set_meta_title(lang('Letters.heading_title'));
		return $this->getList();  
	}
	
	public function add(){
		
		$this->template->set_meta_title(lang('Users.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST'){
			//printr($this->request->getPost()); exit;

            $userid=$this->letteruserModel->insert($this->request->getPost());
		   

		 $this->session->setFlashdata('message', 'User Saved Successfully.');

			return redirect()->to(site_url('letters/user'));
		}
		$this->getForm();
	}
	
	public function edit(){
		
		
		$this->template->set_meta_title(lang('Letters.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST'){	
			$id=$this->uri->getSegment(4);
			$udata=array(
				'user_name'=>$this->request->getPost('user_name'),
				'user_email'=>$this->request->getPost('user_email'),
				'user_place'=>$this->request->getPost('user_place')

			);
			

			$this->letteruserModel->update($id,$this->request->getPost());

			//$this->userModel->updateAssign($id,$this->request->getPost('form_assign'));

			$this->session->setFlashdata('message', 'User Updated Successfully.');
		
			return redirect()->to(site_url('letters/user'));
		}
		$this->getForm();
	}
	
	
	public function delete(){
		if ($this->request->getPost('selected')){
			$selected = $this->request->getPost('selected');
		}else{
			$selected = (array) $this->uri->getSegment(4);
		}
			$this->letteruserModel->deleteLetterUser($selected);
		
		
		$this->session->setFlashdata('message', 'User deleted Successfully.');
		return redirect()->to(site_url('letters/user'));
	}
	
	protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Letters.heading_title'),
			'href' => admin_url('letters')
		);
		
		$this->template->add_package(array('datatable'),true);

		$data['add'] = site_url('letters/user/add');
		$data['delete'] = site_url('letters/user/delete');
		$data['datatable_url'] = site_url('letters/user/search');

		$data['heading_title'] = lang('Letters.heading_title');
		
		$data['text_list'] = lang('Letters.text_list');
		$data['text_no_results'] = lang('Letters.text_no_results');
		$data['text_confirm'] = lang('Letters.text_confirm');
		
		$data['button_add'] = lang('Letters.button_add');
		$data['button_edit'] = lang('Letters.button_edit');
		$data['button_delete'] = lang('Letters.button_delete');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array)$this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}

		return $this->template->view('Admin\Letters\Views\user', $data);
	}
	
	public function search() {
		$requestData= $_REQUEST;
        $totalData = $this->letteruserModel->getTotalLetters();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'order'  		 => $requestData['order'][0]['dir'],
            'sort' 			 => $requestData['order'][0]['column'],
            'start' 			 => $requestData['start'],
            'limit' 			 => $requestData['length']
        );
        $totalFiltered = $this->letteruserModel->getTotalLetters($filter_data);

        $filteredData = $this->letteruserModel->getLetter($filter_data);

        $datatable=array();
        foreach($filteredData as $result) {

            $action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-primary" href="'.site_url('letters/user/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
            $action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.site_url('letters/user/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[]=array(
                '<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
                $result->user_name,
                $result->user_email,
                $result->user_place,
                //$result->status?'Enabled':'Disabled',
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

	public function login(){
	    $id=$this->uri->getSegment(4);
        $user = $this->userModel->find($id);

        $this->session->set('temp_user',$this->user->getUser());
        $this->session->set('user',$user);
        $this->user->assignUserAttr($user);
        return redirect()->to(base_url('admin'));
    }
	
	protected function getForm(){
		
		$this->template->add_package(array('select2'),true);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Users.heading_title'),
			'href' => site_url('letters/user')
		);
		

		$data['heading_title'] 	= lang('Users.heading_title');
		$data['text_form'] = $this->uri->getSegment(3) ? "User Edit" : "User Add";
		$data['text_image'] =lang('Users.text_image');
		$data['text_none'] = lang('Users.text_none');
		$data['text_clear'] = lang('Users.text_clear');
		$data['cancel'] = site_url('letters/user');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$user_info = $this->letteruserModel->find($this->uri->getSegment(4));
		}
		
		foreach($this->letteruserModel->getFieldNames('letter_users') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($user_info->{$field}) && $user_info->{$field}) {
				
				$data[$field] = html_entity_decode($user_info->{$field},ENT_QUOTES, 'UTF-8');
				if($field=="gp"){
					$data[$field]=explode(",",$user_info->{$field});
				}
			} else {
				$data[$field] = '';
			}
		}

		echo $this->template->view('Admin\Letters\Views\userForm',$data);
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->userModel->validationRules;

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
	
	public function assign($id ){

   }
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */