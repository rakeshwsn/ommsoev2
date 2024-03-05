<?php

namespace Admin\Enterprises\Controllers;
use Admin\Dashboard\Models\EnterpriseModel;
use Admin\Dashboard\Models\BlockModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Enterprises\Models\MonthModel;
use Admin\Enterprises\Models\EstablishmentTransactionDetailsModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Dashboard\Models\YearModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EstablishmentTransReport extends AdminController
{
	private $enterprisesModel;
	private $monthModel;
	private $districtModel;
	private $yearModel;
	private $enetrpriseTxnDetails;
	private $blockModel;
	private $gpModel;


	public function __construct()
	{
		$this->enterprisesModel = new EnterprisesModel();
		$this->enetrpriseTxnDetails = new EstablishmentTransactionDetailsModel();
		$this->blockModel = new BlockModel();
		$this->monthModel = new MonthModel();
		$this->districtModel = new DistrictModel();
		$this->yearModel = new YearModel();
	}
	public function index()
	{
		// $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
		$this->template->set('header', true);

		$request = $this->request->getGet('request');

		//month
		$data['months'][0] = 'Select Month';
		$months =  $this->monthModel->findAll();
		foreach ($months as $month) {
			$data['months'][$month->id] = $month->name;
		}

		//year
		$data['years'][0] = 'Select year';
		$years =  $this->yearModel->orderBy('name', 'asc')->findAll();
		foreach ($years as $year) {
			$data['years'][$year->id] = $year->name;
		}

		//year text
		$year_id = $this->request->getGet('year_id');

		if ($year_id !== null && $year_id !== getCurrentYearId()) {
			$data['year_name_text'] =  $this->yearModel->find($year_id)->name;
		} else {
			$data['year_name_text'] = getCurrentYear();
		}

		
		$data['districts'][0] = 'Select Districts';

		if ($this->user->district_id) {
			$districts = $this->districtModel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
			$data['district_id'] = $this->user->district_id;
		} else {
			$data['district_id'] = $this->request->getGet('district_id');
			$districts = $this->districtModel->orderBy('name', 'asc')->findAll();
		}
		if ($data['district_id']) {
			$data['district_text'] = $this->districtModel->find($data['district_id'])->name;
		}

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}

		$data['blocks'][0] = 'Select Block';
		if ($this->user->block_id) {
			$blocks = $this->blockModel->where('id', $this->user->block_id)->orderBy('name', 'asc')->findAll();
			$data['block_id'] = $this->user->block_id;
		} else {
			$data['block_id'] = $this->request->getGet('block_id');
			$blocks = $this->blockModel->orderBy('name', 'asc')->findAll();
		}
		if ($data['block_id']) {
			$data['block_text'] =  $this->blockModel->find($data['block_id'])->name;

		}

		foreach ($blocks as $block) {
			$data['blocks'][$block->id] = $block->name;
		}

		
		//managing unit text
		$management_unit_type = $this->request->getGet('management_unit_type');
		if ($management_unit_type == 0) {
			$data['managementunit_text'] = "all";
		} elseif ($management_unit_type == "shg") {
			$data['managementunit_text'] = "SHG";
		} elseif ($management_unit_type == "fpo") {
			$data['managementunit_text'] = "FPO";
		}

		//month text

		$month_id = $this->request->getGet('month_id');

		if ($month_id && $month_id !== getCurrentMonthId()) {
			$data['month_text'] =  $this->monthModel->find($month_id)->name;
		} else {
			$data['month_text'] =  $this->monthModel->getCurrentMonth()->name;
		}
		$filter = [];
		//block
		
		$data['blocks'][0] = 'Select Block';
		if ($data['district_id']) {

			$blocks =  $this->blockModel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
			foreach ($blocks as $block) {
				$data['blocks'][$block->id] = $block->name;
			}

		}

		$data['district_id'] = $filter['district_id'] = $data['district_id'] ?? 0;
		$data['year_id'] = $filter['year_id'] = $this->request->getGet('year_id') ?? ((new YearModel())->getCurrentYear())->id;
		$data['month_id'] = $filter['month_id'] = $this->request->getGet('month_id') ?? ((new MonthModel())->getCurrentMonth())->id;
		$data['block_id'] = $filter['block_id'] = $data['block_id'] ?? 0;
		$data['management_unit_type'] = $filter['management_unit_type'] = $this->request->getGet('management_unit_type') ?? '';

		$distwisetxnreports = $this->enetrpriseTxnDetails->unitwisereport($filter);
		foreach ($distwisetxnreports as $distwisetxnreport) {
			$data['distwisetxns'][] = [
				'unit_name' => $distwisetxnreport->unit_name,
				'total_units_upto' => $distwisetxnreport->total_units_upto,
				'total_units_mon' => $distwisetxnreport->total_units_mon,
				'total_units_cumm' => $distwisetxnreport->total_units_cumm,
				'turnover_upto' => $distwisetxnreport->turnover_upto,
				'turnover_mon' => $distwisetxnreport->turnover_mon,
				'turnover_cumm' => $distwisetxnreport->turnover_cumm,
				'expn_upto' => $distwisetxnreport->expn_upto,
				'expn_mon' => $distwisetxnreport->expn_mon,
				'expn_cumm' => $distwisetxnreport->expn_cumm,
				'incm_upto' => $distwisetxnreport->incm_upto,
				'incm_mon' => $distwisetxnreport->incm_mon,
				'avg_turnover' => round($distwisetxnreport->avg_turnover, 2)
			];
		}
		$data['download_txn_excel_url'] = admin_url('enterprises/enttxnreport?request=download_txn_excel') . '&' . http_build_query($filter);
		if ($request) {
			$reader = new Html();
			$doc = new \DOMDocument();
			$spreadsheet = new Spreadsheet();
			$sheetindex = 0;

			$filename = 'statewise Report.xlsx';

			// dd($data);
			$htmltable = view('Admin\Enterprises\Views\excelFormEntTransRpt', $data);
			// echo $htmltable;exit;
			$htmltable = preg_replace("/&(?!\S+;)/", "&amp;", $htmltable);
			// dd($htmltable);
			$worksheet = $spreadsheet->createSheet($sheetindex);

			$worksheet->setTitle('MPR of Enterprises Established');

			$reader->setSheetIndex($sheetindex);

			$spreadsheet = $reader->loadFromString($htmltable, $spreadsheet);

			$worksheet = $spreadsheet->getActiveSheet();


			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

			//
			$spreadsheet->setActiveSheetIndex(0);
			$highestColumn = $worksheet->getHighestColumn();

			// Iterate through each column and set auto-size
			for ($col = 'A'; $col <= $highestColumn; $col++) {
				// $worksheet->getColumnDimension($col)->setAutoSize(true);

				$worksheet->getStyle('A1:' . $highestColumn . $worksheet->getHighestRow())
					->getAlignment()->setWrapText(true);
			}
			if ($request == 'download_txn_excel') {

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="' . $filename . '"');
				header('Cache-Control: max-age=0');
				header('Cache-Control: max-age=1');
				$writer = new Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
		}

		return $this->template->view('Admin\Enterprises\Views\establishmentTransactionReport', $data);
	}

	public function ajaxBlocks()
	{

		$data['blocks'] = [];
		$BlocksModel = new BlockModel();

		$district_id = $this->request->getGet('district_id');
		$data['blocks'] = $BlocksModel->where('district_id', $district_id)->orderBy('name', 'asc')->findAll();
		return $this->response->setJSON($data);
	}
}
