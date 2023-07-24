<?php

namespace Admin\Dashboard\Controllers;

use Admin\OdishaMap\Models\OdishaMapModel;
use Admin\Dashboard\Controllers\Api;

use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\AreaCoverageModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;

class AreaCoverage extends AdminController
{
	public function index()
	{
		$areamodel = new AreaCoverageModel();
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

		$filter = [];

		if ($data['year_id'] > 0) {
			$filter = [
				'year_id' =>  $data['year_id']
			];
		}
		$areaList = $areamodel->getAll($filter);

		$data['areas'] = [];

		foreach ($areaList as $row) {
			$data['areas'][] = [
				'year' => $row->year,
				'created_at' => ymdToDmy($row->created_at),
				'edit_url' => admin_url('dashboard/areacoverage/edit?year_id=' . $row->year_id),
			];
		}


		return $this->template->view('Admin\Dashboard\Views\areacoverage', $data);
	}

	public function add()
	{
		$areamodel = new AreaCoverageModel();
		if ($this->request->getMethod(1) == 'POST') {

			//delete if year_id exists
			$areamodel->where('year_id', $this->request->getPost('year_id'))->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$areadata[] = [
					'year_id' => $this->request->getPost('year_id'),
					'district_id' => $key,

					'farmers' => $values['farmers'],
					'achievement' => $values['achievement'],
				];
			}
			$areamodel->insertBatch($areadata);

			return redirect()->to(admin_url('dashboard/areacoverage'))->with('message', 'successful');
		}

		return $this->getForm();
	}

	public function edit()
	{

		$areamodel = new AreaCoverageModel();
		if ($this->request->getMethod(1) == 'POST') {

			$year_id = $this->request->getGet('year_id');

			$areamodel->where('year_id', $year_id)->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$areadata[] = [
					'year_id' => $year_id,
					'district_id' => $key,
					'farmers' => $values['farmers'],
					'achievement' => $values['achievement'],
				];
			}
			$data['areas'] = $areamodel->insertBatch($areadata);

			return redirect()->to(admin_url('dashboard/areacoverage'))->with('message', 'successful');
		}


		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		helper('form');
		$areamodel = new AreaCoverageModel();

		$year_id = $this->request->getGet('year_id');


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

		$distModel = new DistrictModel();

		$data['districts'] = [];

		if ($year_id) {
			$filter = [
				'year_id' => $year_id
			];

			$districts = $areamodel->getOne($filter);

			foreach ($districts as $district) {
				$data['districts'][] = [
					'id' => $district->district_id,
					'name' => $district->district,

					'farmers' => $district->farmers ?: 0,
					'achievement' => $district->achievement ?: 0,

				];
			}

			$data['year_text'] = $yearmodel->find($year_id)->name;
		} else {
			$districts = $distModel->findAll();

			foreach ($districts as $district) {
				$data['districts'][] = [
					'id' => $district->id,
					'name' => $district->name,
					'farmers' => '',
					'achievement' => '',
				];
			}
		}


		return $this->template->view('Admin\Dashboard\Views\areacoverageForm', $data);
	}

	public function chart()
	{
		$this->template->set_meta_title(lang('Pages.heading_title'));

		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select districts';

		$districts = $districtmodel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}
		$data['area_url'] = site_url('api/areacoverage');
		return $this->template->view('Admin\dashboard\Views\areaCoverageChart', $data);
	}
}
