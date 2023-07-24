<?php

namespace Admin\Dashboard\Controllers;

use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\EstablishmentModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;

class Establishment extends AdminController
{
	// private $error = array();

	public function index()
	{
		$establishmentmodel = new EstablishmentModel();
		$this->template->set_meta_title(lang('Pages.heading_title'));

		helper('form');
		$filter = [];
		$establishlist = $establishmentmodel->getAll($filter);
		//  dd($establishlist);
		$data['establishes'] = [];

		foreach ($establishlist as $row) {
			$data['establishes'][] = [
				'created_at' => ymdToDmy($row->created_at),
				'establishment_type' => $row->establishment_type,
				'edit_url' => admin_url('dashboard/establishment/edit?establishment_type=' . $row->establishment_type),
			];
		}
		// dd($data);
		return $this->template->view('Admin\Dashboard\Views\establishment', $data);
	}

	public function add()
	{
		$establishmentmodel = new EstablishmentModel();
		if ($this->request->getMethod(1) == 'POST') {

			//delete if year_id exists
			$establishmentmodel->where('establishment_type', $this->request->getPost('establishment_type'))->delete();
			foreach ($this->request->getPost('district') as $key => $values) {
				$establishdata[] = [
					'district_id' => $key,
					'establishment_type' => $this->request->getPost('establishment_type'),
					'main_center' => $values['main_center'],
					'sub_center' => $values['sub_center'],
				];
			}
			$establishmentmodel->insertBatch($establishdata);

			return redirect()->to(admin_url('dashboard/establishment'))->with('message', 'successful');
		}

		return $this->getForm();
	}

	public function edit()
	{
		$establishmentmodel = new EstablishmentModel();
		if ($this->request->getMethod(1) == 'POST') {

			$establishment_type = $this->request->getGet('establishment_type');

			$establishmentmodel->where('establishment_type', $establishment_type)->delete();
            $dist=$this->request->getPost('district');
			
			foreach ($dist as $key => $values) {
				$establishdata[] = [
					'establishment_type' => $establishment_type,
					'district_id' => $key,
					'main_center' => $values['main_center'],
					'sub_center' => $values['sub_center'],
					
				];
			}
			$data['establishdata'] = $establishmentmodel->insertBatch($establishdata);

			return redirect()->to(admin_url('dashboard/establishment'))->with('message', 'successful');
		}

		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		helper('form');
		$establishmentmodel = new EstablishmentModel();

		$establishment_type = $this->request->getGet('establishment_type');
		//   dd($establishment_type);
		$distModel = new DistrictModel();

		$data['districts'] = [];
		// $data['establishment_text'] = '';
		if ($establishment_type) {
			
			$districts = $establishmentmodel->getDistricts(['establishment_type'=>$establishment_type]);
			//   dd($districts);
			foreach ($districts as $district) {
				$data['districts'][] = [
					'id' => $district->district_id,
					'name' => $district->district,
					'main_center' => $district->main_center ?: 0,
					'sub_center' => $district->sub_center ?: 0,

				];
			}
			$data['establishment_text'] = strtolower($establishment_type)=="chc" ? 'Custom Hiring Center':'Community Managed Seed Centre';
			//  dd($data);
		} else {
			$districts = $distModel->findAll();

			foreach ($districts as $district) {
				$data['districts'][] = [
					'id' => $district->id,
					'name' => $district->name,
					'main_center' => '',
					'sub_center' => '',

				];
			}
		}
		return $this->template->view('Admin\Dashboard\Views\establishmentForm', $data);
	}
	public function chart()
	{
		$data['establish_url'] = site_url('api/establishment');

		return $this->template->view('Admin\dashboard\Views\establishmentChart', $data);
	}
	public function odishamap()
	{
		$odishamapmodel = new EstablishmentModel();
		$data['maps'] = $odishamapmodel->getestablishmentmap();
		// printr($data);
		

		return $this->template->view('Admin\Dashboard\Views\odishamap', $data);
	}
}
