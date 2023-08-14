<?php

namespace Admin\Dashboard\Controllers;


use Admin\Dashboard\Models\DistrictMapModel;
use Admin\Dashboard\Models\DistrictModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;


class DistrictMap extends AdminController
{
	// private $error = array();

    public function index()
	{
		$DistrictMapModel = new DistrictMapModel();
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
		$maplist = $DistrictMapModel->getAll($filter);
		
		$data['maps'] = [];

		foreach($maplist as $row){
			$data['maps'][] = [
				'year' => $row->year,
				'created_at' => ymdToDmy($row->created_at),
				'edit_url' => admin_url('dashboard/map/edit?year_id='.$row->year_id),
			];
		}
		
		return $this->template->view('Admin\Dashboard\Views\dashboardDistrictMap', $data);
	}
	public function add()
	{
		$DistrictMapModel = new DistrictMapModel();
		if ($this->request->getMethod(1) == 'POST') {

			//delete if year_id exists
			$DistrictMapModel->where('year_id', $this->request->getPost('year_id'))->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$mapdata[] = [
					'year_id' => $this->request->getPost('year_id'),
					'district_id' => $key,
					'blocks' =>$values['blocks'],
					'gps' => $values['gps'],
					'villages' => $values['villages'],
					'tentative_farmers' => $values['tentative_farmers'],
					'chcs' => $values['chcs'],
					'cmscs' => $values['cmscs'],
				];
			}
			$DistrictMapModel->insertBatch($mapdata);

			return redirect()->to(admin_url('dashboard/map'))->with('message', 'successful');
		}

		return $this->getForm();
	}

	public function edit()
	{
		              	
		$DistrictMapModel = new DistrictMapModel();
		if ($this->request->getMethod(1) == 'POST') {

			$year_id = $this->request->getGet('year_id');
			
			$DistrictMapModel->where('year_id', $year_id)->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$mapdata[] = [
					'year_id' => $year_id,
					'district_id' => $key,
					'blocks' => $values['blocks'],
					'gps' => $values['gps'],
                    'villages' => $values['villages'],
                    'tentative_farmers' => $values['tentative_farmers'],
					'chcs' => $values['chcs'],
					'cmscs' => $values['cmscs']

				];
			}
			$data['maps']=$DistrictMapModel->insertBatch($mapdata);
			
			return redirect()->to(admin_url('dashboard/map'))->with('message', 'successful');
		}
		

		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		helper('form');
		$DistrictMapModel = new DistrictMapModel();
		
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

		if($year_id){
			$filter = [
				'year_id' => $year_id
			];
			
			$districts = $DistrictMapModel->getOne($filter);

			foreach($districts as $district){
				$data['districts'][] = [
					'id' => $district->district_id,
					'name' => $district->district,
					'blocks' => $district->blocks ?:0,
					'gps' => $district->gps ?: 0,
					'villages' => $district->villages ?: 0,
                    'tentative_farmers' => $district->tentative_farmers ?:0,
					'chcs' => $district->chcs ?:0,
					'cmscs' => $district->cmscs ?:0

				]; 	
			}


		} else {
			$districts = $distModel->findAll();
			
			foreach($districts as $district){
				$data['districts'][] = [
					'id' => $district->id,
					'name' => $district->name,
					'blocks' => '',
					'gps' => '',
                    'villages' => '',
                    'tentative_farmers' => '',
					'chcs' => '',
					'cmscs' => ''

				];
			}
		}
		

		return $this->template->view('Admin\Dashboard\Views\dashboardDistrictMapForm', $data);
	}
}

