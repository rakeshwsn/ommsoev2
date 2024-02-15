<?php

namespace Admin\Localisation\Controllers;

use App\Controllers\AdminController;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\GrampanchayatModel;

class Grampanchayat extends AdminController
{
	private $error = array();
	private $grampanchayatModel;
	private $districtModel;
	private $blockModel;
	public function __construct()
	{
		$this->grampanchayatModel = new GrampanchayatModel();
		$this->blockModel = new BlockModel();
		$this->districtModel = new DistrictModel();
	}
	public function index()
	{
		$this->template->set_meta_title(lang('Grampanchayat|Millets'));
		return $this->getList();
	}
	public function add()
	{

		$this->template->set_meta_title(lang('Grampanchayat Add'));

		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {


			$id = $this->grampanchayatModel->insert($this->request->getPost());
			// printr($this->request->getPost());
			// exit;
			$this->session->setFlashdata('message', 'Grampanchayat Saved Successfully.');

			return redirect()->to(base_url('admin/areacoverage/grampanchayat'));

		}
		$this->getForm();
	}
	public function edit()
	{

		$this->template->set_meta_title(lang('Grampanchayat.heading_title'));

		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
			// printr($_POST);
			// exit;
			$id = $this->uri->getSegment(4);

			$this->grampanchayatModel->update($id, $this->request->getPost());

			$this->session->setFlashdata('message', 'Grampanchayat Updated Successfully.');

			return redirect()->to(base_url('admin/areacoverage/grampanchayat'));


		}
		$this->getForm();
	}

	public function delete()
	{
		if ($this->request->getPost('selected')) {
			$selected = $this->request->getPost('selected');
		} else {
			$selected = (array) $this->uri->getSegment(4);
		}

		$this->grampanchayatModel->delete($selected);

		$this->session->setFlashdata('message', 'Grampanchayat deleted Successfully.');
		return redirect()->to(base_url('admin/areacoverage/grampanchayat'));
	}
	protected function getList()
	{

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

		$data['add'] = admin_url('grampanchayat/add');
		$data['delete'] = admin_url('grampanchayat/delete');
		$data['datatable_url'] = admin_url('grampanchayat/search');

		$data['heading_title'] = lang('Grampanchayat');

		$data['text_list'] = lang('Grampanchayat.text_list');
		$data['text_no_results'] = lang('Grampanchayat.text_no_results');
		$data['text_confirm'] = lang('sure you want to delete Grampanchayat');

		$data['button_add'] = lang('Grampanchayat.button_add');
		$data['button_edit'] = lang('Grampanchayat.button_edit');
		$data['button_delete'] = admin_url('grampanchayat/delete');

		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array) $this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}
		$data['districts'] = $this->districtModel->getAll();
		$data['district_id'] = $this->user->district_id;
		$data['block_id'] = $this->user->block_id;

		return $this->template->view('Admin\Localisation\Views\grampanchayat', $data);
	}

	public function search()
	{
		$requestData = $_REQUEST;
		$totalData = $this->grampanchayatModel->getTotals();

		$totalFiltered = $totalData;

		//For filtering at block level
		if ($this->user->block_id) {
			$requestData['block'] = $this->user->block_id;
		}
		if ($this->user->district_id) {
			$requestData['district'] = $this->user->district_id;
		}

		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'filter_district' => $requestData['district'],
			'filter_block' => $requestData['block'],
			'filter_grampanchayat' => $requestData['grampanchayat'],
			'order' => $requestData['order'][0]['dir'],
			'sort' => $requestData['order'][0]['column'],
			'start' => $requestData['start'],
			'limit' => $requestData['length']
		);

		$totalFiltered = $this->grampanchayatModel->getTotals($filter_data);

		$filteredData = $this->grampanchayatModel->getAll($filter_data);

		$datatable = array();

		foreach ($filteredData as $result) {

			$action = '<div class="btn-group btn-group-sm pull-right">';
			$action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('grampanchayat/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
			if ($this->user->isAdmin()) {
				$action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('grampanchayat/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			}
			$action .= '</div>';

			$datatable[] = array(
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				$result->name,
				$result->district,
				$result->block,
				$action
			);
		}
		//printr($datatable);
		$json_data = array(
			"draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
			"recordsTotal" => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data" => $datatable
		);

		return $this->response->setContentType('application/json')
			->setJSON($json_data);
	}

	protected function getForm()
	{

		$this->template->add_package(array('select2'), true);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);

		$_SESSION['isLoggedIn'] = true;
		// $data['heading_title'] = lang('Grampanchayat.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Grampanchayat Edit" : "Grampanchayat Add";
		$data['cancel'] = admin_url('areacoverage/grampanchayat');

		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}

		//Saraswatee code
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$grampanchayat_info = $this->grampanchayatModel->find($this->uri->getSegment(4));
		}

		foreach ($this->grampanchayatModel->getFieldNames('soe_grampanchayats') as $field) {
			if ($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if (isset($grampanchayat_info->{$field}) && $grampanchayat_info->{$field}) {
				$data[$field] = html_entity_decode($grampanchayat_info->{$field}, ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}

		$data['districts'][0] = 'Select districts';
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
		} else {
			$data['district_id'] = $this->user->district_id;
		}

		foreach ($this->districtModel->orderBy('name', 'asc')->getAll() as $dist) {
			$data['districts'][$dist->id] = $dist->name;
		}
		//distrcit end
		//Blocks start
		$data['blocks'][0] = 'Select block';
		$blocks = $this->blockModel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();

		foreach ($blocks as $block) {
			$data['blocks'][$block->id] = $block->name;
		}

		if ($this->request->getGet('block_id')) {
			$data['block_id'] = $this->request->getGet('block_id');
		} else {
			$data['block_id'] = $this->user->block_id;
		}

		if ($this->request->isAJAX()) {

			echo $this->template->view('Admin\Localisation\Views\grampanchayatform', $data, true);
		} else {
			echo $this->template->view('Admin\Localisation\Views\grampanchayatform', $data);
		}
	}
	protected function validateForm()
	{
		$this->validation = \Config\Services::validation();
		$id = $this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->grampanchayatModel->validationRules;

		if ($this->validate($rules)) {
			return true;
		} else {
			$this->error['warning'] = "Warning: Please check the form carefully for errors!";
			return false;
		}
		return !$this->error;
	}

	public function ajaxAdd()
	{
		$data['id'] = null;
		$data['status'] = false;
		$data['message'] = 'Failed to add Grampanchayat';

		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {

			$post = [
				'block_id' => (int) $this->request->getPost('block_id'),
				'name' => trim($this->request->getPost('name')),
				'lgd_code' => (int) $this->request->getPost('lgd_code'),
			];
			$id = $this->grampanchayatModel->insert($post);

			if ($id) {
				$data['id'] = $id;
				$data['status'] = true;
				$data['message'] = 'Successfully added Grampanchayat';
			}
		}
		if ($this->error) {
			$validation_errors = $this->validation->getErrors();
			$data['message'] = implode("\n", $validation_errors);
			;
		}
		return $this->response->setJSON($data);
	}
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */