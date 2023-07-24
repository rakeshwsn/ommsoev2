<?php

namespace Admin\Dashboard\Controllers;
use Admin\Dashboard\Models\DistrictModel;

use Admin\Dashboard\Models\ProcurementModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;
use phpDocumentor\Reflection\PseudoTypes\True_;

class Procurement extends AdminController
{
	// private $error = array();
	public function index()
	{
		$procurementmodel = new ProcurementModel();

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
		$procureList = $procurementmodel->getAll($filter);
		
		$data['procures'] = [];

		foreach($procureList as $row){
			$data['procures'][] = [
				'year' => $row->year,
				'total_districts' => $row->total_districts,
				'total_qty' => $row->total_qty,
				'total_amount' => number_format($row->total_amount,2),
				'total_farmers' => $row->total_farmers,
				'created_at' => ymdToDmy($row->created_at),
				'edit_url' => admin_url('dashboard/procurement/edit?year_id='.$row->year_id),
			];
		}
		
		
		return $this->template->view('Admin\Dashboard\Views\procurement', $data);
	}

	public function add()
	{
		$procurementmodel = new procurementmodel();
		if ($this->request->getMethod(1) == 'POST') {

			//delete if year_id exists
			$procurementmodel->where('year_id', $this->request->getPost('year_id'))->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$procuredata[] = [
					'year_id' => $this->request->getPost('year_id'),
					'district_id' => $key,
					'quantity' => $values['quantity'],
					'farmers' => $values['farmers'],
				];
			}
			$procurementmodel->insertBatch($procuredata);

			return redirect()->to(admin_url('dashboard/Procurement'))->with('message', 'successful');
		}

		return $this->getForm();
	}

	public function edit()
	{
		              	
		$procurementmodel = new procurementmodel();
		if ($this->request->getMethod(1) == 'POST') {	
			$year_id = $this->request->getGet('year_id');
		
			$procurementmodel->where('year_id', $year_id)->delete();

			foreach ($this->request->getPost('district') as $key => $values) {
				$procuredata[] = [
					'year_id' => $year_id,
					'district_id' => $key,
					'quantity' => $values['quantity'],
					'farmers' => $values['farmers'],
				];
			}
			$data['procures']=$procurementmodel->insertBatch($procuredata);
			
			return redirect()->to(admin_url('dashboard/Procurement'))->with('message', 'successful');
		}
		return $this->getForm();
	}

	private function getForm()
	{
		$data = [];
		helper('form');
		$procurementmodel = new procurementmodel();
		
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
			
			$districts = $procurementmodel->getDistrictWise($filter);

			foreach($districts as $district){
				$data['districts'][] = [
					'id' => $district->district_id,
					'name' => $district->district,
					'quantity' => $district->quantity,
					'farmers' => $district->farmers ?: 0,
				
				]; 	
			}

			$data['year_text'] = $yearmodel->find($year_id)->name;

		} else {
			$districts = $distModel->findAll();
			
			foreach($districts as $district){
				$data['districts'][] = [
					'id' => $district->id,
					'name' => $district->name,
					'quantity'=>'',
					'farmers' => '',
				];
			}
		}
		

		return $this->template->view('Admin\Dashboard\Views\procurementForm', $data);
	}
	public function chart()
	{
		$this->template->set_meta_title(lang('Pages.heading_title'));

		helper('form');

		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select districts';

		$districts = $districtmodel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}


		 $data['procurement_url'] = site_url('api/procurement');

		return $this->template->view('Admin\Dashboard\Views\procurementChart',$data);

	}

	

}
