<?php

namespace Admin\Dashboard\Controllers;

use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\EnterpriseModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use phpDocumentor\Reflection\PseudoTypes\True_;

class Enterprise extends AdminController
{
	// private $error = array();

	public function index()
	{
		$enterpriseunitmodel = new EnterprisesUnitModel();
		$yearmodel = new YearModel();
		$districtmodel = new DistrictModel();
		$enterprisesmodel = new EnterpriseModel();
		$this->template->set_meta_title(lang('Pages.heading_title'));

		helper('form');

		$data['years'][0] = 'Select years';

		$data['districts'] = $districtmodel->findAll();
		$years = $enterprisesmodel->getYears();
		foreach ($years as $year) {
			$data['years'][$year->year_id] = $year->years;
		}

		$data['year_id'] = $this->request->getGet('year_id');

		$data['year_id'] = $filter['year_id'] = $this->request->getGet('year_id') ?? ((new YearModel())->getCurrentYear())->id;

		$enterprisesList = $enterprisesmodel->getList($filter);

		$data['enterprises'] = [];
		foreach ($enterprisesList as $row) {
			$data['enterprises'][] = [
				'entunits' => $row->unit_name,
				'total_units' => $row->total_units,
				'edit_url' => admin_url('dashboard/enterprise/edit?year_id=' . $data['year_id'] . '&unit_id=' . $row->unit_id),
			];
		}

		return $this->template->view('Admin\Dashboard\Views\enterprise', $data);
	}


	public function edit()
	{

		$enterprisesmodel = new EnterpriseModel();
		if ($this->request->getMethod(1) == 'POST') {

			$year_id = $this->request->getGet('year_id');
			$unit_id = $this->request->getGet('unit_id');
			$enterprisesmodel->where('year_id', $year_id)->where('unit_id', $unit_id)->delete();
			foreach ($this->request->getPost('district') as $key => $values) {
				$enterprisesdata[] = [
					'year_id' => $year_id,
					'district_id' => $key,
					'unit_id' => $unit_id,
					'wshg' => $values['wshg'],
					'fpos' => $values['fpos'],

				];
			}
			// dd($enterprisesdata);
			$data['enterprises'] = $enterprisesmodel->insertBatch($enterprisesdata);

			return redirect()->to(admin_url('dashboard/enterprise'))->with('message', 'update successful');
		}

		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		$enterprisesmodel = new EnterpriseModel();
		$enterpriseunitmodel = new EnterprisesUnitModel();
		$districtmodel = new DistrictModel();
		$yearmodel = new YearModel();

		$enterprisesList = [];
		$year_id = $this->request->getGet('year_id');
		$unit_id =$this->request->getGet('unit_id');

		$data['enterprisesList'] = $enterprisesmodel->getDataBYUnit($year_id, $unit_id);


		if ($this->request->getGet('unit_id') && ($this->request->getGet('year_id'))) {

			foreach ($enterprisesList as $enterprise) {
				$data['enterprisesList'][] = [
					'unit_id' => $enterprise->unit_id,
					'year_id' => $enterprise->year_id,
					'district_id' => $enterprise->d_id,
					'district' => $enterprise->district,
					'wshg' => $enterprise->wshg ?: 0,
					'fpos' => $enterprise->fpos ?: 0,

				];
			}
		}
		$unit_id = $this->request->getGet('unit_id');


		if (isset($unit_id)) {
			$data['unit_name'] = $enterpriseunitmodel->find($unit_id)->name;
		}


		$year_id = $this->request->getGet('year_id');
		if ($year_id) {
			$data['year_name'] = $yearmodel->find($year_id)->name;
		}
		// dd($data);

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
