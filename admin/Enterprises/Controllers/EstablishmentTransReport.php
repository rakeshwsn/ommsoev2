<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\BlockModel;
use Admin\Enterprises\Models\DistrictModel;
use Admin\Enterprises\Models\MonthModel;
// use Admin\Enterprises\Models\EnterprisesBudgetModel;
// use Admin\Enterprises\Models\EnterprisesModel;
// use Admin\Enterprises\Models\EnterprisesUnitModel;
// use Admin\Enterprises\Models\GpModel;
// use Admin\Enterprises\Models\VillagesModel;
use Admin\Enterprises\Models\YearModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class EstablishmentTransReport extends AdminController
{
	public function index()
	{
		// $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
		$this->template->set('header', true);
		// helper('form');
		// $enterprisesmodel = new EnterprisesModel();
		$distModel = new DistrictModel();
		$blockmodel = new BlockModel();
		$yearmodel = new YearModel();
		$monthmodel = new MonthModel();
		// $enterprisesunitmodel = new EnterprisesUnitModel();
		$data['districts'][0] = 'Select District';
		$districts = $distModel->orderBy('name', 'asc')->findAll();
		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}

		$data['district_id'] = 0;
		//month
		$data['months'][0] = 'Select Month';
		$months = $monthmodel->orderBy('name', 'asc')->findAll();
		foreach ($months as $month) {
			$data['months'][$month->id] = $month->name;
		}

		$data['month_id'] = 0;
		//year
		$data['years'][0] = 'Select year';
		$years = $yearmodel->orderBy('name', 'asc')->findAll();
		foreach ($years as $year) {
			$data['years'][$year->id] = $year->name;
		}

		$data['year_id'] = 0;
		//block
		$data['blocks'][0] = 'Select Block';
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');

			$blocks = $blockmodel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
			foreach ($blocks as $block) {
				$data['blocks'][$block->id] = $block->name;
			}
		}
		$data['block_id'] = 0;
		if ($this->request->getGet('block_id')) {
			$data['block_id'] = $this->request->getGet('block_id');
		}
		$data['management_unit_type'] = '';
		if ($this->request->getGet('management_unit_type')) {
			$data['management_unit_type'] = $this->request->getGet('management_unit_type');
		}
		$data['unit_type'] = '';
		if ($this->request->getGet('unit_type')) {
			$data['unit_type'] = $this->request->getGet('unit_type');
		}
		// dd($data);
		return $this->template->view('Admin\Enterprises\Views\establishmentTransactionReport',$data);
	}
	public function ajaxBlocks()
	{

		$data['blocks'] = [];
		$BlocksModel = new BlockModel();

		$district_id = $this->request->getGet('district_id');
		$data['blocks'] = $BlocksModel->where('district_id', $district_id)->orderBy('name', 'asc')->findAll();
		// printr($data);
		return $this->response->setJSON($data);
	}
}
