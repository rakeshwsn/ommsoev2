<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\BlockModel;
use Admin\Enterprises\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesBudgetModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\GpModel;
use Admin\Enterprises\Models\VillagesModel;
use Admin\Enterprises\Models\YearModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class Enterprises extends AdminController
{
	public function index()
	{
		$this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);

		helper('form');
		$enterprisesmodel = new EnterprisesModel();
		$distModel = new DistrictModel();
		$data['districts'][0] = 'Select District';

		$districts = $distModel->findAll();
		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}


		$blockmodel = new BlockModel();
		$data['blocks'][0] = 'Select Block';
		$data['district_id'] = 0;
		$data['block_id'] = 0;
		$data['year'] = 0;
		$data['years'][0] = 'Select DOE';

		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
			$blocks = $blockmodel->where('district_id', $data['district_id'])->findAll();

			$data['blocks'][0] = 'Select Block';
			foreach ($blocks as $block) {
				$data['blocks'][$block->id] = $block->name;
			}
		}
		if ($this->request->getGet('district_id')) {
			$district_id = $this->request->getGet('district_id');
			$years = $enterprisesmodel->yearWise($district_id);

			$data['years'][0] = 'Select DOE';
			foreach ($years as $year) {
				$data['years'][$year->id] = $year->year;
			}
		}
		// dd($data);

		if ($this->request->getGet('block_id')) {

			$data['block_id'] = $this->request->getGet('block_id');
		}
		// dd($data['block_id']);
		$data['management_unit_type'] = 0;
		if ($this->request->getGet('management_unit_type')) {
			$data['management_unit_type'] = $this->request->getGet('management_unit_type');
		}
		// dd($data['management_unit_type']);
	
		// dd($this->request->getGet('doeyear'));
		if ($this->request->getGet('year')) {
			$data['year'] = $this->request->getGet('year');
		}
		


		$filter = [];
		if ($data['district_id'] > 0) {
			$filter['district_id'] = $data['district_id'];
		}

		if ($data['block_id'] > 0) {
			$filter['block_id'] = $data['block_id'];
		}
		if ($data['management_unit_type'] > 0) {
			$filter['management_unit_type'] = $data['management_unit_type'];
		}
		if ($data['year'] > 0) {
			$filter['YEAR(e.date_estd)'] = $data['year'];
		}

// dd($filter);
		$enterpriseslist = $enterprisesmodel->getAll($filter);
		
		// dd($enterpriseslist);
		// exit;
		$data['enterprises'] = [];

		foreach ($enterpriseslist as $row) {
			$data['enterprises'][] = [
				'districts' => $row->districts,
				'blocks' => $row->blocks,
				'gp' => $row->gp,
				'villages' => $row->villages,
				'management_unit_type' => $row->management_unit_type,
				'managing_unit_name' => $row->managing_unit_name,
				'date_estd' => $row->date_estd,
				'mou_date' => $row->mou_date,
				'year' => $row->year,
				'edit_url' => admin_url('enterprises/edit?id=' . $row->id),

			];
		}
		// dd($data);

		$data['excel_link'] = admin_url('enterprises/exceldownld');

		// printr($enterpriseslist);
		// exit;
		return $this->template->view('Admin\Enterprises\Views\establishment', $data);
	}


	public function download()
	{
		$enterprisesmodel = new EnterprisesModel();
		$totalEntData = $enterprisesmodel->getAll();
		$worksheet_unit = [];
		$data['entdatas'] = [];

		foreach ($totalEntData as $row) {
			$data['entdatas'][] = [
				'unit_name' => $row->unit_name,
				'districts' => $row->districts,
				'blocks' => $row->blocks,
				'gp' => $row->gp,
				'villages' => $row->villages,
				'management_unit_type' => $row->management_unit_type,
				'managing_unit_name' => $row->managing_unit_name,
				'date_estd' => $row->date_estd,
				'mou_date' => $row->mou_date,
				'contact_person' => $row->contact_person,
				'unit_budget' => $row->unit_budget,
				'addl_budget' => $row->addl_budget,
				'purpose_infr_support' => $row->is_support_basis_infr,
				'support_infr_amount' => $row->is_support_basis_infr,
				'contact_mobile' => $row->contact_mobile,
			];
		}

		$filename = 'Enterprise-Establishment' . '.xlsx';


		$sheetindex = 0;
		$reader = new Html();
		$doc = new \DOMDocument();
		$spreadsheet = new Spreadsheet();

		$htmltable = view('Admin\Enterprises\Views\excelFormEnt', $data);

		$htmltable = preg_replace("/&(?!\S+;)/", "&amp;", $htmltable);

		$worksheet = $spreadsheet->createSheet($sheetindex);

		$reader->setSheetIndex($sheetindex);
		$worksheet->setTitle("Enterprises");

		$spreadsheet = $reader->loadFromString($htmltable, $spreadsheet);

		$worksheet = $spreadsheet->getActiveSheet();

		// Load HTML content into a DOM object for formatting from class
		$doc->loadHTML($htmltable);

		$rows = $doc->getElementsByTagName('tr');

		//formatting and designing
		foreach ($worksheet->getRowIterator() as $row) {
			// Find the corresponding row element in the HTML table
			$rowIndex = $row->getRowIndex();

			$rowElement = $rows->item($rowIndex - 1); // -1 because row indices start at 1 in PhpSpreadsheet

			// Get the class name of the row element
			$className = $rowElement->getAttribute('class');
		}
		// Assuming $worksheet is your PhpSpreadsheet worksheet object

		// Get the highest column index
		$highestColumn = $worksheet->getHighestColumn();

		// Iterate through each column and set auto-size
		for ($col = 'A'; $col <= $highestColumn; $col++) {
			$worksheet->getColumnDimension($col)->setAutoSize(true);
		}

		//remove the default worksheet
		$spreadsheet->removeSheetByIndex(
			$spreadsheet->getIndex(
				$spreadsheet->getSheetByName('Worksheet')
			)
		);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit();
	}
	public function add()
	{


		if ($this->request->getMethod(1) == 'POST'  && $this->validateForm()) {
			// dd($this->request->getPost());
			$enterprisesmodel = new EnterprisesModel();
			$enterprisesmodel->where('id', $this->request->getGet('id'))->delete();

			$enterprisesdata[] = [
				'unit_id' => $this->request->getPost('unit_id'),
				'district_id' => $this->request->getPost('district_id'),
				'block_id' => $this->request->getPost('block_id'),
				'gp_id' => $this->request->getPost('gp_id'),
				'village_id' => $this->request->getPost('village_id'),
				'budget_fin_yr_id' => $this->request->getPost('budget_fin_yr_id'),
				'management_unit_type' => $this->request->getPost('management_unit_type'),
				'managing_unit_name' => $this->request->getPost('managing_unit_name'),
				'contact_person' => $this->request->getPost('contact_person'),
				'contact_mobile' => $this->request->getPost('contact_mobile'),
				'date_estd' => $this->request->getPost('date_estd'),
				'mou_date' => $this->request->getPost('mou_date'),
				'unit_budget' => $this->request->getPost('unit_budget'),
				'addl_budget' => $this->request->getPost('addl_budget'),
				'unit_budget_amount' => $this->request->getPost('unit_budget_amount'),
				'is_support_basis_infr' => $this->request->getPost('is_support_basis_infr'),
				'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
				'support_infr_amount' => $this->request->getPost('support_infr_amount')
			];
			// dd($enterprisesdata);

			$enterprisesmodel->insertBatch($enterprisesdata);

			return redirect()->to(admin_url('enterprises'))->with('message', 'successful');
		}

		return $this->getForm();
	}
	public function edit()
	{

		$enterprisesmodel = new EnterprisesModel();
		if ($this->request->getMethod(1) == 'POST'  && $this->validateForm()) {
			$id = $this->request->getGet('id');
			$district_id = $this->request->getPost('district_id');
			$block_id = $this->request->getPost('block_id');
			$enterprisesmodel->where('id', $id)->delete();
			$enterprisesdata[] = [
				'unit_id' => $this->request->getPost('unit_id'),
				'district_id' => $district_id,
				'block_id' => $block_id,
				'gp_id' => $this->request->getPost('gp_id'),
				'village_id' => $this->request->getPost('village_id'),
				'budget_fin_yr_id' => $this->request->getPost('budget_fin_yr_id'),
				'management_unit_type' => $this->request->getPost('management_unit_type'),
				'managing_unit_name' => $this->request->getPost('managing_unit_name'),
				'contact_person' => $this->request->getPost('contact_person'),
				'contact_mobile' => $this->request->getPost('contact_mobile'),
				'date_estd' => $this->request->getPost('date_estd'),
				'mou_date' => $this->request->getPost('mou_date'),
				'unit_budget' => $this->request->getPost('unit_budget'),
				'addl_budget' => $this->request->getPost('addl_budget'),
				'unit_budget_amount' => $this->request->getPost('unit_budget_amount'),
				'is_support_basis_infr' => $this->request->getPost('is_support_basis_infr'),
				'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
				'support_infr_amount' => $this->request->getPost('support_infr_amount')
			];

			$data['enterprises'] = $enterprisesmodel->insertBatch($enterprisesdata);

			return redirect()->to(admin_url('enterprises'))->with('message', 'successful');
		}


		return $this->getForm();
	}

	public function ajaxBlocks()
	{

		$data['blocks'] = [];
		$BlocksModel = new BlockModel();

		$district_id = $this->request->getGet('district_id');
		$data['blocks'] = $BlocksModel->where('district_id', $district_id)->findAll();
		// printr($data);
		return $this->response->setJSON($data);
	}
	public function ajaxDoe()
	{

		$data['years'] = [];
		$enterprisesmodel = new EnterprisesModel();
		$district_id = $this->request->getGet('district_id');
		$data['years'] = $enterprisesmodel->yearWise($district_id);
		// printr($data);
		return $this->response->setJSON($data);
	}
	public function ajaxgps()
	{

		$data['gps'] = [];
		$gpmodel = new GpModel();

		$block_id = $this->request->getGet('block_id');

		$data['gps'] = $gpmodel->where('block_id', $block_id)->findAll();

		return $this->response->setJSON($data);
	}
	public function ajaxvillages()
	{

		$data['villages'] = [];
		$villagemodel = new VillagesModel();

		$gp_id = $this->request->getGet('gp_id');

		$data['villages'] = $villagemodel->where('gp_id', $gp_id)->findAll();

		return $this->response->setJSON($data);
	}

	private function getForm()
	{
		$enterprisesmodel = new EnterprisesModel();
		// district start
		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select districts';

		$districts = $districtmodel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}
		$yearmodel = new YearModel();
		$data['budget_fin_yrs'][0] = 'Select Budget Year';

		$budget_fin_yrs = $yearmodel->findAll();

		foreach ($budget_fin_yrs as $budget_fin_yr) {
			$data['budget_fin_yrs'][$budget_fin_yr->id] = $budget_fin_yr->name;
		}
		//district end
		//block start
		$blockmodel = new BlockModel();
		$data['blocks'][0] = 'Select blocks';

		if ($this->request->getPost('district_id')) {
			$blockModel = new BlockModel();

			$blocks = $blockModel->where('district_id', $this->request->getPost('district_id'))->findAll();

			foreach ($blocks as $block) {

				$data['blocks'][$block->id] = $block->name;
			}
		}
		$data['block_id'] = 0;
		if ($this->request->getPost('block_id')) {
			$data['block_id'] = $this->request->getPost('block_id');
		}
		//block end
		//gp start
		$data['gps'][0] = 'Select Gp';

		if ($this->request->getPost('gp_id')) {
			$gpmodel = new GpModel();

			$gps = $gpmodel->where('block_id', $this->request->getPost('block_id'))->findAll();

			foreach ($gps as $gp) {

				$data['gps'][$gp->id] = $gp->name;
			}
		}
		$data['gp_id'] = 0;
		if ($this->request->getPost('gp_id')) {
			$data['gp_id'] = $this->request->getPost('gp_id');
		}
		//gp end
		//villages start
		$data['villages'][0] = 'Select villages';

		if ($this->request->getPost('gp_id')) {
			$villagemodel = new VillagesModel();

			$villages = $villagemodel->where('gp_id', $this->request->getPost('gp_id'))->findAll();

			foreach ($villages as $village) {

				$data['villages'][$village->id] = $village->name;
			}
		}
		$data['village_id'] = 0;
		if ($this->request->getPost('village_id')) {
			$data['village_id'] = $this->request->getPost('village_id');
		}
		//village end
		$enterprisesbudgetmodel = new EnterprisesBudgetModel();
		$data['unit_budgets'][0] = 'Select budgets';
		// $budget_id = [];
		$unit_budgets = $enterprisesbudgetmodel->findAll();

		foreach ($unit_budgets as $unit_budget) {
			$data['unit_budgets'][$unit_budget->id] = $unit_budget->budget_code;
		}
		$enterprisesbudgetmodel = new EnterprisesBudgetModel();
		$data['addl_budgets'][0] = 'Select budgets';
		// $budget_id = [];
		$addl_budgets = $enterprisesbudgetmodel->findAll();

		foreach ($addl_budgets as $addl_budget) {
			$data['addl_budgets'][$addl_budget->id] = $addl_budget->budget_code;
		}

		$enterprisesunitmodel = new EnterprisesUnitModel();
		$data['units'][0] = 'Select Units';

		$units = $enterprisesunitmodel->findAll();

		foreach ($units as $unit) {
			$data['units'][$unit->id] = $unit->name;
		}
		$data['management_unit_types'] = [
			'SHG' => 'SHG',
			'FPO' => 'FPO',
		];
		$data['is_support'] = [
			'no' => 'No',
			'yes' => 'Yes',
		];
		// dd($data);
		$id = [];
		if ($this->request->getGet('id')) {
			$enterprise =  $enterprisesmodel->where('id', $this->request->getGet('id'))->first();
			// printr($data); exit;
			$data['enterprise_text'] = "Edit Enterprise Data";

			foreach ($enterprise as $col => $value) {
				$data[$col] = $value;
			}
		} else {
			$enterprise = $enterprisesmodel->db->getFieldData('enterprises');
			// dd($enterprise);
			foreach ($enterprise as $value) {
				$data['enterprise_text'] = "Add Enterprise Data";
				$data[$value->name] = '';
			}
		}
		//  dd($data);
		return $this->template->view('Admin\Enterprises\Views\addEstablishment', $data);
	}


	protected function validateForm()
	{
		$enterprisesmodel = new EnterprisesModel();
		$validation =  \Config\Services::validation();
		$id = $this->uri->getSegment(4);

		$rules = $enterprisesmodel->validationRules;
		// dd($this->request->getPost());
		if ($this->validate($rules)) {
			return true;
		} else {
			// dd($validation->getErrors());
			$this->error['warning'] = "Warning: Please check the form carefully for errors!";
			return false;
		}
		return !$this->error;
	}
}
