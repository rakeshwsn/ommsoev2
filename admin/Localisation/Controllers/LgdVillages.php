<?php

namespace Admin\Localisation\Controllers;

use Admin\Localisation\Models\LgdBlocksModel;
use App\Controllers\AdminController;
use Admin\Localisation\Models\LgdVillagesModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\LgdGpsModel;

class LgdVillages extends AdminController
{
	private $error = array();
	private $villageModel;

	public function __construct()
	{
		$this->villageModel = new LgdVillagesModel();
	}

	public function index()
	{
		$this->template->set_meta_title(lang('Village.heading_title'));
		// echo"dadad"
		return $this->getList();
	}


	protected function getList()
	{

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Village.heading_title'),
			'href' => admin_url('lgdvillage')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

	
		$data['datatable_url'] = admin_url('lgdvillage/search');

		$data['heading_title'] = lang('Village.heading_title');

		$data['text_list'] = lang('Village.text_list');
		$data['text_no_results'] = lang('Village.text_no_results');
		$data['text_confirm'] = lang('Village.text_confirm');

		

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
		$data['district_id'] = $this->user->lgd_code;
		$data['block_id'] = $this->user->block_lgd_code;
		$data['blocks'] = [];
		$data['gp_id'] = $this->user->gp_lgd_code;
		$data['grampanchayat'] = [];
		return $this->template->view('Admin\Localisation\Views\lgdvillage', $data);
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

	
	//Saraswatee code end
	public function grampanchayat()
	{

		$data['grampanchayat'] = [];
		$gpmodel = new LgdGpsModel();

		$block_id = $this->request->getGet('block_id');
		
		$data['grampanchayat'] = $gpmodel->where('block_lgd_code', $block_id)->orderBy('name', 'asc')->findAll();
		
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