<?php

namespace Admin\Dashboard\Controllers;

use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\EnterpriseModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;
use phpDocumentor\Reflection\PseudoTypes\True_;

class Enterprise extends AdminController
{
	// private $error = array();

	public function index()
	{
		$enterprisesmodel = new EnterpriseModel();
		$this->template->set_meta_title(lang('Pages.heading_title'));

		helper('form');

		$yearmodel = new YearModel();
		$data['years'][0] = 'Select years';

		$years = $yearmodel->findAll();

		foreach ($years as $year) {
			$data['years'][$year->id] = $year->name;
		}

		$data['year_id'] = 0;

		if ($this->request->getGet('year_id')) {
			$data['year_id'] = $this->request->getGet('year_id');
		}
		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select District';
		$districts = $districtmodel->findAll();
		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}
		$data['district_id'] = 0;
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
		}
		// dd($data);

		$filter = [];

		if ($data['year_id']) {
			$filter['year_id'] = $data['year_id'];
		}
		if ($data['district_id']) {
			$filter['district_id'] = $data['district_id'];
		}

		//  dd($filter);

		$enterprisesList = $enterprisesmodel->getAll($filter);
		// dd($enterprisesList);
		$data['enterprises'] = [];

		foreach ($enterprisesList as $row) {
			$data['enterprises'][] = [
				'year' => $row->year,
				'district_name' => $row->district_name,
				'edit_url' => admin_url('dashboard/enterprise/edit?year_id=' . $row->year_id . '&district_id=' . $row->district_id),
			];
		}
		//  dd($data);
		return $this->template->view('Admin\Dashboard\Views\enterprise', $data);
	}
	public function add()
	{
	
		$enterprisesmodel = new EnterpriseModel();
		if ($this->request->getMethod(1) == 'POST') {

			$year_id = $this->request->getGet('year_id');
			$district_id = $this->request->getGet('district_id');
			$enterprisesmodel->where('year_id', $year_id)->where('district_id', $district_id)->delete();

			foreach ($this->request->getPost('unit_name') as $key => $enterprise) {
				$enterprisesdata[] = [
					'year_id' => $year_id,
					'district_id' => $district_id,
					'unit_id' => $key,
					'unit_name' => $enterprise['name'],
					'wshg' => $enterprise['wshg'],
					'fpos' => $enterprise['fpos'],
				];
				// dd($enterprisesdata);
			}
			// echo "<pre>";
			// print_r($enterprisesdata);
			// exit;
			$data['enterprises'] = $enterprisesmodel->insertBatch($enterprisesdata);
			//   dd($data);
			return redirect()->to(admin_url('dashboard/enterprise'))->with('message', 'update successful');
		}

		return $this->getForm();
	}

	public function edit()
	{

		$enterprisesmodel = new EnterpriseModel();
		if ($this->request->getMethod(1) == 'POST') {

			$year_id = $this->request->getGet('year_id');
			$district_id = $this->request->getGet('district_id');
			$enterprisesmodel->where('year_id', $year_id)->where('district_id', $district_id)->delete();

			foreach ($this->request->getPost('unit_name') as $key => $enterprise) {
				$enterprisesdata[] = [
					'year_id' => $year_id,
					'district_id' => $district_id,
					'unit_id' => $key,
					'unit_name' => $enterprise['name'],
					'wshg' => $enterprise['wshg'],
					'fpos' => $enterprise['fpos'],
				];
				// dd($enterprisesdata);
			}
			// echo "<pre>";
			// print_r($enterprisesdata);
			// exit;
			$data['enterprises'] = $enterprisesmodel->insertBatch($enterprisesdata);
			//   dd($data);
			return redirect()->to(admin_url('dashboard/enterprise'))->with('message', 'update successful');
		}

		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		helper('form');
		$enterprisesmodel = new EnterpriseModel();
		$yearmodel = new YearModel();

		$districtmodel = new DistrictModel();
		$data['year_id'] = 0;
		$year_id = $this->request->getGet('year_id');
		// dd($year_id);
		$district_id = $this->request->getGet('district_id');

		//    dd($district_id);

		$data['units'] = [];

		$filter = [];
		if ($year_id) {
			$filter['year_id'] = $year_id;
		}
		if ($district_id) {
			$filter['district_id'] = $district_id;
		}
		$enterprisesList = $enterprisesmodel->getByDistYear($filter);
		//  dd($enterprisesList);
		foreach ($enterprisesList as $enterprise) {
			$data['units'][] = [
				'unit_id' => $enterprise->unit_id,
				'unit_name' => $enterprise->unit_name,
				'wshg' => $enterprise->wshg ?: 0,
				'fpos' => $enterprise->fpos ?: 0,

			];
		}
		// $data['year_text'] = $yearmodel->find($year_id)->name;
		// $data['district_text'] = $districtmodel->find($district_id)->name;


		return $this->template->view('Admin\Dashboard\Views\enterpriseForm', $data);
	}

	public function sync()
	{
		$options = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);
		$jsonData = file_get_contents('https://soe1.milletsodisha.com/api/dashboard/enterpriseDistrictwise', false, stream_context_create($options));

		$jsonData = json_decode($jsonData, TRUE);
		$enterprisesmodel = new EnterpriseModel();

		$enterprisesmodel->where('deleted_at IS NULL')->delete();

		foreach ($jsonData as $data) {
			$enterprisesdata[] = [
				'year_id' => $data['year_id'],
				'district_id' => $data['district_id'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'wshg' => $data['total_units_shg'],
				'fpos' => $data['total_units_fpo'],
			];
		}
		// dd($enterprisesdata);
		$data['enterprises'] = $enterprisesmodel->insertBatch($enterprisesdata);
		// dd($data);
		return redirect()->to(admin_url('dashboard/enterprise'))->with('message', 'successful');
	}

	public function chart()
	{
		$yearmodel = new YearModel();
		$data['years'][0] = 'Select years';

		$years = $yearmodel->findAll();

		foreach ($years as $year) {
			$data['years'][$year->id] = $year->name;
		}

		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select districts';

		$districts = $districtmodel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}


		$data['enterprise_url'] = site_url('api/enterprise');

		return $this->template->view('Admin\Dashboard\Views\enterpriseChart', $data);
	}

	
}
