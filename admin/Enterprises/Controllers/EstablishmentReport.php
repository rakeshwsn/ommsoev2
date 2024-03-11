<?php

namespace Admin\Enterprises\Controllers;

use Admin\Dashboard\Models\BlockModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Enterprises\Models\MonthModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Dashboard\Models\YearModel;
use App\Controllers\AdminController;
use Config\ExcelStyles;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EstablishmentReport extends AdminController
{
	private $districtModel;
	private $blockModel;
	private $yearModel;
	private $enterprisesModel;
	private $monthModel;

	public function __construct()
	{
		$this->districtModel = new DistrictModel();
		$this->blockModel = new BlockModel();
		$this->yearModel = new YearModel();
		$this->enterprisesModel = new EnterprisesModel();
		$this->monthModel = new MonthModel();
	}
	public function index()
	{
		$this->template->set('header', true);
		$request = $this->request->getGet('request');
		$data = [];
		$data = $this->filterOptions($data);
		//district text
		if ($data['district_id']) {
			$data['district_text'] = $this->districtModel->find($data['district_id'])->name;
		}
		//block text
		if ($data['block_id']) {
			$data['block_text'] = $this->blockModel->find($data['block_id'])->name;
		}

		//year text

		$year_id = $this->request->getGet('year_id');
		if ($year_id) {
			$data['year_text'] = $this->yearModel->find($year_id)->name;
		}
		//unit text
		$management_unit_type = $this->request->getGet('management_unit_type');

		if ($management_unit_type === "FPO") {
			$data['unit_text'] = "FPO";
		} elseif ($management_unit_type === "SHG") {
			$data['unit_text'] = "SHG";
		} elseif ($management_unit_type === "all") {
			$data['unit_text'] = "FPO & SHG";
		}

		//month text
		$month_id = $this->request->getGet('month_id');
		if ($month_id) {
			$data['month_text'] = $this->monthModel->find($month_id)->name;
		}

		$filter = [
			'district_id' => $data['district_id'],
			'block_id' => $data['block_id'],
			'management_unit_type' => $this->request->getGet('management_unit_type'),
			'year_id' => $this->request->getGet('year_id'),
			'month' => $this->request->getGet('month_id'),
			'unit_type' => $this->request->getGet('unit_type'),
		];

		//Retrive data for gps if block_id present
		if ($data['block_id']) {
			$gpUnits = $this->enterprisesModel->gpwiseUnits($filter);
			
			$data['gpUnits'] = [];
			foreach ($gpUnits as $gpunit) {
				$_gp_units = [];
				foreach ($gpUnits as $_gp_unit) {
					if ($gpunit->gp_id == $_gp_unit->gp_id) {
						$_gp_units[$_gp_unit->unit_id] = $_gp_unit->total_units;
					}
				}
				//gpwise total of all thresher

				$total = 0;
				foreach ($_gp_units as $_gp_unit) {
					$total += $_gp_unit;
				}
				$_gp_units['total'] = $total;

				$data['gpUnits'][$gpunit->gp_id] = [

					'gp' => $gpunit->gp,
					'g_units' => $_gp_units
				];
			}
			
			//unitwise total for all district --add new row as total
			$total_units = $_gp_units = [];
			$total = 0;
			foreach ($gpUnits as $_g_unit) {
				$total_units[$_g_unit->unit_id][] = $_g_unit->total_units;
				$total += $_g_unit->total_units;
			}

			$total_units['total'][] = $total;

			foreach ($total_units as $unit_id => $utotal) {
				$_gp_units[$unit_id] = array_sum($utotal);
			}

			$data['gpUnits']['total'] = [
				'gp' => 'Total',
				'g_units' => $_gp_units
			];
			$block_id = $this->request->getGet('block_id');
		}

		//Retrive data for blocks if district_id is present
		else if ($data['district_id']) {
			$blockUnits = $this->enterprisesModel->blockwiseUnits($filter);
			$data['blockUnits'] = [];
			foreach ($blockUnits as $blockunit) {
				$_block_units = [];
				foreach ($blockUnits as $_block_unit) {
					if ($blockunit->block_id == $_block_unit->block_id) {
						$_block_units[$_block_unit->unit_id] = $_block_unit->total_units;
					}
				}
				//blockwise total of all unit
				$total = 0;
				foreach ($_block_units as $_block_unit) {
					$total += $_block_unit;
				}
				$_block_units['total'] = $total;

				$data['blockUnits'][$blockunit->block_id] = [
					'block' => $blockunit->block,
					'b_units' => $_block_units
				];
			}

			//unitwise total for all district --add new row as total
			$total_units = $_block_units = [];
			$total = 0;
			foreach ($blockUnits as $_b_unit) {
				$total_units[$_b_unit->unit_id][] = $_b_unit->total_units;
				$total += $_b_unit->total_units;
			}

			$total_units['total'][] = $total;

			foreach ($total_units as $unit_id => $utotal) {
				$_block_units[$unit_id] = array_sum($utotal);
			}

			$data['blockUnits']['total'] = [
				'block' => 'Total',
				'b_units' => $_block_units
			];
		} else {

			$units = $this->enterprisesModel->districtwiseUnits($filter);
			$data['units'] = [];
			foreach ($units as $unit) {
				//for all units of each district
				$_units = [];
				foreach ($units as $_unit) {
					if ($unit->district_id == $_unit->district_id) {
						$_units[$_unit->unit_id] = $_unit->total_units;
					}
				}
				//districtwise total of all thresher
				$total = 0;
				foreach ($_units as $_unit) {
					$total += $_unit;
				}
				$_units['total'] = $total;

				//for all districts
				$data['units'][$unit->district_id] = [
					'district' => $unit->district,
					'units' => $_units
				];
			}

			//unitwise total for all district --add new row as total
			$total_units = $_units = [];
			$total = 0;
			foreach ($units as $_unit) {
				$total_units[$_unit->unit_id][] = $_unit->total_units;
				$total += $_unit->total_units;
			}
			$total_units['total'][] = $total;

			foreach ($total_units as $unit_id => $utotal) {
				$_units[$unit_id] = array_sum($utotal);
			}

			$data['units']['total'] = [
				'district' => 'Total',
				'units' => $_units
			];
		}

		$data['unit_names'] = (new EnterprisesUnitModel)->orderBy('id')->findAll();

		$data['download_excel_url'] = admin_url('enterprises/report?request=download_excel') . '&' . http_build_query($filter);
		$data['download_pdf_url'] = admin_url('enterprises/report?request=download_pdf') . '&' . http_build_query($filter);
		//for excel download
		if ($request) {
			$reader = new Html();
			$doc = new \DOMDocument();
			$spreadsheet = new Spreadsheet();
			$sheetindex = 0;

			$filename = 'Enterprise Establishment Report.xlsx';

			$htmltable = view('Admin\Enterprises\Views\excelFormEntRpt', $data);
			$htmltable = preg_replace("/&(?!\S+;)/", "&amp;", $htmltable);
			$worksheet = $spreadsheet->createSheet($sheetindex);

			$worksheet->setTitle('Enterprise Establishment Report');

			$reader->setSheetIndex($sheetindex);

			$spreadsheet = $reader->loadFromString($htmltable, $spreadsheet);

			$worksheet = $spreadsheet->getActiveSheet();


			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

			$spreadsheet->setActiveSheetIndex(0);

			if ($request == 'download_excel') {

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="' . $filename . '"');
				header('Cache-Control: max-age=0');
				header('Cache-Control: max-age=1');
				$writer = new Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			} elseif ($request == 'download_pdf') {

				$filename = 'Enterprise Establishment Report.pdf';

				// $spreadsheet->getStyle('A1:D4')->applyFromArray(ExcelStyles::border());

				// Provide the download link for the generated PDF file
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment;filename="' . $filename . '"');
				header('Cache-Control: max-age=0');
				header('Cache-Control: max-age=1');

				// Use PhpSpreadsheet's Dompdf writer to generate PDF
				$writer = IOFactory::createWriter($spreadsheet, 'Mpdf');
				$writer->save('php://output');

				exit();
			}
		}
		return $this->template->view('Admin\Enterprises\Views\establishmentReport', $data);
	}

	private function filterOptions($data)
	{

		$data['districts'][0] = 'Select District';

		if ($this->user->district_id) {
			$districts =  $this->districtModel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
		} else {
			$districts =  $this->districtModel->orderBy('name', 'asc')->findAll();
		}
		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}
		$data['district_id'] = $this->user->district_id;
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
		}

		//month
		// if user block_id avaliable populate block
		$data['blocks'][0] = 'Select Block';

		if ($this->user->block_id) {
            $blocks =  $this->blockModel->where('id', $this->user->block_id)->orderBy('name', 'asc')->findAll();
            $data['block_id'] = $this->user->block_id;

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        } else {
            $blocks =  $this->blockModel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
            $data['block_id'] = $this->request->getGet('block_id');

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        }
		//year populate
		$data['years'][0] = 'Select year';

		$years = $this->yearModel->orderBy('name', 'asc')->findAll();
		foreach ($years as $year) {
			$data['years'][$year->id] = $year->name;
		}
		 // month
		$data['months'][0] = 'Select Month';
		$data['month_id'] = 0;
		$months = $this->monthModel->orderBy('name', 'asc')->findAll();
		foreach ($months as $month) {
			$data['months'][$month->id] = $month->name;
		}
		if ($this->request->getGet('month_id')) {
			$data['month_id'] = $this->request->getGet('month_id');
		}
		$data['year_id'] = 0;
		if ($this->request->getGet('year_id')) {
			$data['year_id'] = $this->request->getGet('year_id');
		}
		$data['block_id'] = 0;

		if ($this->request->getGet('block_id')) {
			$data['block_id'] = $this->request->getGet('block_id');
		}
		if ($this->user->block_id) {
            $data['block_id'] = $this->user->block_id;
        }
		$data['management_unit_type'] = '';

		if ($this->request->getGet('management_unit_type')) {
			$data['management_unit_type'] = $this->request->getGet('management_unit_type');
		}
		$data['unit_type'] = '';

		if ($this->request->getGet('unit_type')) {
			$data['unit_type'] = $this->request->getGet('unit_type');
		}
		return $data;
	}
	public function ajaxBlocks()
	{

		$data['blocks'] = [];
		$data['district_id'] = $this->request->getGet('district_id');
		if ($this->user->block_id) {
			$data['blocks'] = $this->blockModel->where('id', $this->user->block_id)->orderBy('name', 'asc')->findAll();
		} else {
			$data['blocks'] = $this->blockModel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
		}
		return $this->response->setJSON($data);
	}
}
