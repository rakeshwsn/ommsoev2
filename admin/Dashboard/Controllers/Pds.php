<?php

namespace Admin\Dashboard\Controllers;

use Admin\Dashboard\Models\districtmodel;

use Admin\Dashboard\Models\PdsModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;
use phpDocumentor\Reflection\PseudoTypes\True_;

class Pds extends AdminController
{
	public function index()
	{
		$pdsmodel = new PdsModel();
		$yearmodel = new YearModel();
		$data['distributed_years'][0] = 'Select years';

		$years = $yearmodel->findAll();

		foreach ($years as $year) {
			$data['distributed_years'][$year->id] = $year->name;
		}

		$data['distributed_year_id'] = 0;


		if ($this->request->getGet('distributed_year_id')) {
			$data['distributed_year_id'] = $this->request->getGet('distributed_year_id');
		}

		$filter = [];

		if ($data['distributed_year_id'] > 0) {
			$filter = [
				'distributed_year_id' =>  $data['distributed_year_id']
			];
		}
		$pdsList = $pdsmodel->getAll($filter);

		$data['pdses'] = [];

		foreach ($pdsList as $row) {
			$data['pdses'][] = [
				'year' => $row->year,
				'districts_covered' => $row->districts_covered,
				'total_qty' => $row->total_qty,
				'total_chb' => $row->total_chb,
				'created_at' => ymdToDmy($row->created_at),
				'edit_url' => admin_url('dashboard/pds/edit?distributed_year_id=' . $row->distributed_year_id),
			];
		}


		return $this->template->view('Admin\dashboard\Views\pds', $data);
	}

	public function add()
	{
		$pdsmodel = new PdsModel();
		if ($this->request->getMethod(1) == 'POST') {

			//delete if purchase_year_id exists
			$pdsmodel->where('distributed_year_id', $this->request->getPost('distributed_year_id'))->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$pdsdata[] = [
					'distributed_year_id' => $this->request->getPost('distributed_year_id'),
					'district_id' => $key,
					'quantity' => $values['quantity'],
					'card_holders_benifited' => $values['card_holders_benifited'],
				];
			}
			$pdsmodel->insertBatch($pdsdata);

			return redirect()->to(admin_url('dashboard/pds'))->with('message', 'successful');
		}

		return $this->getForm();
	}

	public function edit()
	{

		$pdsmodel = new PdsModel();
		if ($this->request->getMethod(1) == 'POST') {

			$distributed_year_id = $this->request->getGet('distributed_year_id');

			$pdsmodel->where('distributed_year_id', $distributed_year_id)->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$pdsdata[] = [
					'distributed_year_id' => $this->request->getGet('distributed_year_id'),
					'district_id' => $key,
					'quantity' => $values['quantity'],
					'card_holders_benifited' => $values['card_holders_benifited'],

				];
			}
			$data['pdses'] = $pdsmodel->insertBatch($pdsdata);

			return redirect()->to(admin_url('dashboard/pds'))->with('message', 'successful');
		}


		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		helper('form');
		$pdsModel = new PdsModel();
		$distributed_year_id = $this->request->getGet('distributed_year_id');
		$year_id = $this->request->getGet('year_id');

		$yearmodel = new YearModel();
		$data['distributed_years'][0] = 'Select years';

		$distributed_years = $yearmodel->findAll();

		foreach ($distributed_years as $distributed_year) {
			$data['distributed_years'][$distributed_year->id] = $distributed_year->name;
		}

		$data['distributed_year_id'] = 0;

		if ($this->request->getGet('distributed_year_id')) {
			$data['distributed_year_id'] = $this->request->getGet('distributed_year_id');
		}

		$districtModel = new DistrictModel();

		$data['districts'] = [];

		if ($distributed_year_id) {
			$filter = [
				'distributed_year_id' => $distributed_year_id
			];
			$districts = $pdsModel->getDistrictWise($filter);

			foreach ($districts as $district) {
				$data['districts'][] = [
					'id' => $district->district_id,
					'name' => $district->district,
					'quantity' => $district->quantity,
					'card_holders_benifited' => $district->card_holders_benifited ?: 0,
				];
			}
			$data['pds_text'] = $yearmodel->find($distributed_year_id)->name;
		} else {
			$allDistricts = $districtModel->findAll();

			foreach ($allDistricts as $district) {
				$data['districts'][] = [
					'id' => $district->id,
					'name' => $district->name,
					'quantity' => '',
					'card_holders_benifited' => '',
				];
			}
		}
		return $this->template->view('Admin\dashboard\Views\pdsForm', $data);
	}
	public function chart()
	{
		
		$data['pds_url'] = site_url('api/pds');

		return $this->template->view('Admin\dashboard\Views\pdsChart', $data);
	}
}
