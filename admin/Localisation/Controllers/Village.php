<?php

namespace Admin\Localisation\Controllers;

use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;
use Admin\Localisation\Models\VillageModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;

class Village extends AdminController
{
	private $error = array();
	private $villageModel;

	public function __construct()
	{
		$this->villageModel = new VillageModel();
	}

	public function index()
	{
		$this->template->set_meta_title(lang('Village.heading_title'));
		// echo"dadad"
		return $this->getList();
	}

	public function add()
	{
		$this->template->set_meta_title(lang('Village.heading_title'));
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
			$data = [
				'gp_id' => $this->request->getPost('gp_id'),
				'name' => $this->request->getPost('name'),
			];

			$id = $this->villageModel->insert($data);
			$this->session->setFlashdata('message', 'village Saved Successfully.');

			return redirect()->to(base_url('admin/village'));
			
		}
		return $this->getForm();
	}

	public function edit()
	{
		$this->template->set_meta_title(lang('Village.heading_title'));
		$id = $this->uri->getSegment(4);
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
			$data = [
				'gp_id' => $this->request->getPost('gp_id'),
				'name' => $this->request->getPost('name'),
			];

			$edit_data = $this->villageModel->update($id, $data);

			$this->session->setFlashdata('message', 'Village Updated Successfully.');

			return redirect()->to(base_url('admin/village'));
		}
		return $this->getForm();
	}

	public function delete()
	{
		if ($this->request->getPost('selected')) {
			$selected = $this->request->getPost('selected');
		} else {
			$selected = (array) $this->uri->getSegment(4);
		}
		$this->villageModel->delete($selected);

		$this->session->setFlashdata('message', 'Village deleted Successfully.');
		return redirect()->to(base_url('admin/village'));
	}

	protected function getList()
	{

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Village.heading_title'),
			'href' => admin_url('village')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

		$data['add'] = admin_url('village/add');
		$data['delete'] = admin_url('village/delete');
		$data['datatable_url'] = admin_url('village/search');

		$data['heading_title'] = lang('Village.heading_title');

		$data['text_list'] = lang('Village.text_list');
		$data['text_no_results'] = lang('Village.text_no_results');
		$data['text_confirm'] = lang('Village.text_confirm');

		$data['button_add'] = lang('Village.button_add');
		$data['button_edit'] = lang('Village.button_edit');
		$data['button_delete'] = lang('Village.button_delete');

		if (isset($this->error['warning'])) {
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array)$this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}

		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();

		return $this->template->view('Admin\Localisation\Views\village', $data);
	}

	public function search()
	{
		$requestData = $_REQUEST;
		$totalData = $this->villageModel->getTotals();
		$totalFiltered = $totalData;

		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'filter_district' => $requestData['district'],
			'filter_block' => $requestData['block'],
			'filter_grampanchayat' => $requestData['grampanchayat'],
			'filter_village' => $requestData['village'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$totalFiltered = $this->villageModel->getTotals($filter_data);

		$filteredData = $this->villageModel->getAll($filter_data);

		$datatable = array();
		foreach ($filteredData as $result) {

			$action  = '<div class="btn-group btn-group-sm pull-right">';
			$action .= 		'<a class="btn btn-sm btn-primary" href="' . admin_url('village/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('village/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';

			$datatable[] = array(
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				$result->name,
				$result->district,
				$result->block,
				$result->grampanchayat,
				$action
			);
		}
		// dd($datatable);
		$json_data = array(
			"draw"            => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $datatable
		);

		return $this->response->setContentType('application/json')
			->setJSON($json_data);
	}

	protected function getForm()
	{
		$gpmodel = new GrampanchayatModel();
		$districtmodel = new DistrictModel();
		$blockmodel = new BlockModel();
		$villagemodel = new VillageModel();
		$this->template->add_package(array('colorbox', 'select2'), true);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Village.heading_title'),
			'href' => admin_url('village')
		);


		$_SESSION['isLoggedIn'] = true;

		$data['heading_title'] 	= lang('Village.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Village Edit" : "Village Add";
		$data['cancel'] = admin_url('village');

		if (isset($this->error['warning'])) {
			$data['error'] 	= $this->error['warning'];
		}
		//Saraswatee code start
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$village_info = $this->villageModel->getDataById($this->uri->getSegment(4));
		}

		// dd($village_info);
		$columns = ['district_id', 'gp_id', 'block_id', 'village_name', 'village_id'];
		foreach ($columns as $field) {
			if ($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if (isset($village_info->$field) && $village_info->$field) {
				$data[$field] = html_entity_decode($village_info->$field, ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}
		// dd($village_info);
		//district start
		$data['districts'][0] = 'Select district';
		if($this->request->getGet('district_id')){
			$data['district_id'] = $this->request->getGet('district_id');
		}

		foreach ($districtmodel->orderBy('name', 'asc')->getAll() as $dist) {
			$data['districts'][$dist->id] = $dist->name;
		}
		// dd($data['district_id']);
		//distrcit end
		//Blocks start
		$data['blocks'][0] = 'Select block';
		$blocks=$blockmodel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
		// dd($blocks);
		foreach ($blocks as $block) {
			$data['blocks'][$block->id] = $block->name;
		}
		if($this->request->getGet('block_id')){
			$data['block_id'] = $this->request->getGet('block_id');
		}
		// dd($data);
		//blocks end
		//gp start
		$data['gps'][0] = 'Select GP';
		$gps = $gpmodel->where('block_id', $data['block_id'])->orderBy('name', 'asc')->findAll();
		foreach ($gps as $gp) {
			$data['gps'][$gp->id] = $gp->name;
		}
		if($this->request->getGet('gp_id')){
			$data['gp_id'] = $this->request->getGet('gp_id');
		}
		if ($this->request->isAJAX()) {

			return $this->template->view('Admin\Localisation\Views\villageForm', $data, true);
		} else {
			return $this->template->view('Admin\Localisation\Views\villageForm', $data);
		}
	}
	//Saraswatee code end
	public function grampanchayat()
	{

		$data['gps'] = [];
		$gpmodel = new GrampanchayatModel();

		$block_id = $this->request->getGet('block_id');
		// dd($block_id);
		$data['gps'] = $gpmodel->where('block_id', $block_id)->orderBy('name', 'asc')->findAll();
		// printr($data['gps']);
		return $this->response->setJSON($data);
	
	}
	public function block()
	{

		$data['blocks'] = [];
		$BlocksModel = new BlockModel();

		$district_id = $this->request->getGet('district_id');
		$data['blocks'] = $BlocksModel->where('district_id', $district_id)->orderBy('name', 'asc')->findAll();
		// printr($data);
		return $this->response->setJSON($data);
	}

	protected function validateForm()
	{
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id = $this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->villageModel->validationRules;

		if ($this->validate($rules)) {
			return true;
		} else {
			//printr($validation->getErrors());
			$this->error['warning'] = "Warning: Please check the form carefully for errors!";
			return false;
		}
		return !$this->error;
	}
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */