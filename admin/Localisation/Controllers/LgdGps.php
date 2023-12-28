<?php
namespace Admin\Localisation\Controllers;

use App\Controllers\AdminController;
use Admin\Localisation\Models\LgdBlocksModel;
use Admin\Localisation\Models\ClusterModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\LgdGpsModel;

class LgdGps extends AdminController
{
	private $error = array();
	private $grampanchayatModel;
	private $districtModel;
	private $blockModel;
	public function __construct()
	{
		$this->grampanchayatModel = new LgdGpsModel();
		$this->blockModel = new LgdBlocksModel();
		$this->districtModel = new DistrictModel();
	}
	public function index()
	{
		$this->template->set_meta_title(lang('Grampanchayat|Millets'));
		return $this->getList();
	}
	
	protected function getList()
	{

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('lgdgp')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

		$data['datatable_url'] = admin_url('lgdgp/search');

		$data['heading_title'] = lang('Grampanchayat');

		$data['text_list'] = lang('Grampanchayat.text_list');
		$data['text_no_results'] = lang('Grampanchayat.text_no_results');
		$data['text_confirm'] = lang('sure you want to delete Grampanchayat');

	

		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array) $this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}
		$data['districts'] = $this->districtModel->getAll();
		$data['district_id'] = $this->user->lgd_code;
		$data['block_id'] = $this->user->block_lgd_code;
		$data['blocks'] = [];

		return $this->template->view('Admin\Localisation\Views\lgdgrampanchayat', $data);
	}

	public function search()
	{
		$requestData = $_REQUEST;
		$totalData = $this->grampanchayatModel->getTotals();

		$totalFiltered = $totalData;

		//For filtering at block level
		if ($this->user->block_lgd_code) {
			$requestData['block'] = $this->user->block_lgd_code;
		}
		if ($this->user->lgd_code) {
			$requestData['district'] = $this->user->district_lgd_code;
		}

		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'filter_district' => $requestData['district'],
			'filter_block' => $requestData['block'],
		
			'order' => $requestData['order'][0]['dir'],
			'sort' => $requestData['order'][0]['column'],
			'start' => $requestData['start'],
			'limit' => $requestData['length']
		);
		
		$totalFiltered = $this->grampanchayatModel->getTotals($filter_data);

		$filteredData = $this->grampanchayatModel->getAll($filter_data);

		$datatable = array();

		foreach ($filteredData as $result) {

		
			
		

			$datatable[] = array(
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				$result->name,
				$result->district,
				$result->block,
			
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
	public function block()
	{

		$data['blocks'] = [];
		$BlocksModel = new LgdBlocksModel();

		$district_id = $this->request->getGet('district_id');
	
		$data['blocks'] = $BlocksModel->where('district_lgd_code', $district_id)->orderBy('name', 'asc')->findAll();
		
		return $this->response->setJSON($data);
	}



	protected function validateForm()
	{
		//printr($_POST);
		$validation = \Config\Services::validation();
		$id = $this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->grampanchayatModel->validationRules;

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