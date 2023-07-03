<?php

namespace Admin\Incentive\Controllers;

use Admin\Incentive\Models\IncentivemainModel;
use Admin\Incentive\Models\IncentiveModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Incentive extends AdminController
{
	private $error = array();
	//private $incentiveModel;

	public function __construct()
	{
		$this->incentiveModel = new IncentiveModel();
		$this->incentivemainModel = new IncentivemainModel();
	}

	public function index()
	{
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		return $this->getListMain();
	}

	protected function getListMain()
	{
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$this->template->add_package(array('datatable', 'select2'), true);
		$data['addform'] = admin_url('incentive/addform');
		$user  = service('user');
		$userDis = $user->district_id;

		$data['searchview'] = admin_url('incentive/incentivesearch/');

		$data['heading_title'] = lang('Incentive.heading_title');
		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');

		$data['button_add'] = lang('Incentive.button_add');
		$data['button_view'] = lang('Incentive.button_view');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');

		$data['session_id'] =  $this->session->get('user')->id;

		$data['datatable_url'] = admin_url('incentive/searchmain');
		if (isset($this->error['warning'])) {
			$data['error'] 	= $this->error['warning'];
		}

		$this->filterOptionsMain($data);

		return $this->template->view('Admin\Incentive\Views\incentivemain', $data);
	}

	public function searchMain()
	{
		$requestData = $_REQUEST;
		$totalData = $this->incentivemainModel->getTotal();
		$totalFiltered = $totalData;
		$filter_data = array(
			'filter_district'	=> $requestData['searchBydistrictId'],
			'filter_block'	    => $requestData['searchByblockId'],
			'filter_year'		=> $requestData['searchByYear'],
			'filter_season'     =>	$requestData['searchBySeason'],
			'filter_search'     => 	$requestData['search']['value'],
			'order'  		    => $requestData['order'][0]['dir'],
			'sort' 			    => $requestData['order'][0]['column'],
			'start' 		    => 	$requestData['start'],
			'limit' 		    =>	$requestData['length']
		);

		$totalFiltered = $this->incentivemainModel->getTotal($filter_data);
		$filteredData = $this->incentivemainModel->getAll($filter_data);

		//  printr($filteredData); exit;
		$datatable = array();
		foreach ($filteredData as $result) {

			if ($result->year == 1) {
				$year = '2017-18';
			} elseif ($result->year == 2) {
				$year = '2018-19';
			} elseif ($result->year == 3) {
				$year = '2019-20';
			} elseif ($result->year == 4) {
				$year = '2020-21';
			} elseif ($result->year == 5) {
				$year = '2021-22';
			}


			if ($result->season == 1) {
				$season = 'kharif';
			} elseif ($result->season == 2) {
				$season = 'Rabi';
			}

			$action  = '<div class="btn-group btn-group-sm pull-right">';
			$action .= 		'<a class="btn btn-sm btn-primary" href="' . admin_url('incentive/view/' . $result->id) . '">View All Data</a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('incentivemain/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';

			if ($result->pdf) {
				$pdfupload = '<a href="' . base_url() . '/uploads/farmerincentive/' . $result->pdf . '">';
				$pdfupload .= '<i class="fa fa-file-pdf-o" style="font-size:48px;color:red"></i>';
				$pdfupload .= '</a>';
			} else {
				$pdfupload = '<h5>Not Uploaded </h5>';
			}

			//$verified = '<input type="checkbox" class="verifyCheck" value="'.$result->id.'" name="verify"'. ($result->verify == 1 ? ' checked' : '') . '/>';
			$verified = '<label class="css-control css-control-primary css-switch">';
			$verified .= '<input type="checkbox" class="css-control-input verifyCheck" value="' . $result->id . '" name="verify"' . ($result->verify == 1 ? ' checked' : '') . '/>';
			$verified .= '<span class="css-control-indicator"></span> Verified</label>';
			$datatable[] = array(
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				$result->district_name,
				$result->block_name,
				$year,
				$season,
				$pdfupload,
				$verified,
				$result->phase,
				$action
			);
		}
		// Calculate the correct pagination count
	$recordsTotal = intval($totalData);
	$recordsFiltered = intval($totalFiltered);

	$json_data = array(
		"draw"            => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
		"recordsTotal"    => $recordsTotal,
		"recordsFiltered" => $recordsFiltered,
		"data"            => $datatable
	);

// 	print_r($json_data);
// exit;
		return $this->response->setContentType('application/json')
			->setJSON($json_data);
	}


	public function incentivesearch()
	{
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$this->template->add_package(array('datatable', 'select2'), true);
		$data['addform'] = admin_url('incentive/addform');
		// $data['searchview'] = admin_url('incentive/incentivesearch/$userDis');

		$data['heading_title'] = lang('Incentive.heading_title');

		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');

		$data['button_add'] = lang('Incentive.button_add');
		$data['button_view'] = lang('Incentive.button_view');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');
		// $data['datatable_url'] = admin_url('incentive/searchall');
		if (isset($this->error['warning'])) {
			$data['error'] 	= $this->error['warning'];
		}
		$this->filterOptionsMain($data);
		$user  = service('user');
		$userDis = $user->district_id;

		$requestData = $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		$data['selectedYear'] = 1;
		// Set the default value for the selected year to 1

		if ($this->request->getGet('year')) {
			$data['selectedYear'] = $this->request->getGet('year');
		}
		$filter_data = array(

			'filter_district' => $userDis ? $userDis : $this->request->getGet('district_id'),
			'filter_block' => $this->request->getGet('block_id'),
			'filter_year' => $this->request->getGet('year') ? $this->request->getGet('year') : 1,
			'filter_season' => $this->request->getGet('season'),
		);
		//print_r($filter_data); exit;
		//$totalFiltered = $this->incentiveModel->getTotal($filter_data);
		$filteredData = $this->incentivemainModel->getAllsearch($filter_data);
	

		$currentUrl = admin_url('incentive/incentivesearch');
		$url = [
			//'currentUrl' => current_url('incentive/incentivesearch'),
			'district_id' => $this->request->getGet('district_id'),
			'block_id'  => $this->request->getGet('block_id'),
			'year'       =>  $this->request->getGet('year'),
			'season'	=>   $this->request->getGet('season'),
			'download' => 'excel'
		];
		$queryString = http_build_query($url);



		if (!isset($url['district_id']) || !isset($url['block_id']) || !isset($url['year']) || !isset($url['season'])) {
			$mergedUrl = $currentUrl . '?' . $queryString;
		} else {
			$mergedUrl = $currentUrl . '?'  . $queryString;
		}
		$data['mergedUrl'] = $mergedUrl;
		$download = $this->request->getGet('download');

		if ($download) {
			$this->dataToExcel($filteredData);
		}

		//printr($filteredData);
		$data['datatable'] = array();
		$year = '';
		foreach ($filteredData as $result) {
			if ($result['year'] == 1) {
				$year = '2017-18';
			} elseif ($result['year'] == 2) {
				$year = '2018-19';
			} elseif ($result['year'] == 3) {
				$year = '2019-20';
			} elseif ($result['year'] == 4) {
				$year = '2020-21';
			} elseif ($result['year'] == 5) {
				$year = '2021-22';
			}




			if ($result['season'] == 1) {
				$season = 'kharif';
			} elseif ($result['season'] == 2) {
				$season = 'Rabi';
			}


			$data['datatable'][$result['district_name']][$result['phase']][] = array(

				'district_name' => strtoupper($result['district_name']),
				'block_name' => strtoupper($result['block_name']),
				'year' => $year,
				'season' => strtoupper($season),
				'gp' => strtoupper($result['gp']),
				'village' => strtoupper($result['village']),
				'name' => strtoupper($result['name']),
				'spouse_name' => strtoupper($result['spouse_name']),
				'gender' => strtoupper($result['gender']),
				'caste' => strtoupper($result['caste']),
				'phone_no' => $result['phone_no'],
				'aadhar_no' => $result['aadhar_no'],
				'year_support' => $result['year_support'],
				'area_hectare' => $result['area_hectare'],
				'bank_name' => strtoupper($result['bank_name']),
				'account_no' => $result['account_no'],
				'ifsc' => $result['ifsc'],
				'amount' => $result['amount'],
				'phase' => $result['phase'],
				'pdf' => $result['pdf'],

			);
		}

		// echo "<pre>";
		// print_r($data['datatable']); exit;

		return $this->template->view('Admin\Incentive\Views\incentiveallview', $data);
	}
	protected function dataToExcel($data)
	{
		$spreadsheet = new Spreadsheet();

		// Create a new sheet
		$sheet = $spreadsheet->getActiveSheet();

		// Set some sample data


		$sheet->setCellValue('A1', 'District');
		$sheet->setCellValue('B1', 'Block');
		$sheet->setCellValue('C1', 'Year');
		$sheet->setCellValue('D1', 'Season');
		$sheet->setCellValue('E1', 'GP');
		$sheet->setCellValue('F1', 'Village');
		$sheet->setCellValue('G1', 'Farmer');
		$sheet->setCellValue('H1', 'Spouse Name');
		$sheet->setCellValue('I1', 'Gender');
		$sheet->setCellValue('J1', 'CASTE');
		$sheet->setCellValue('K1', 'Mobile');
		$sheet->setCellValue('L1', 'AADHAAR');
		$sheet->setCellValue('M1', 'Year of Support');
		$sheet->setCellValue('N1', 'Area in Hectare');
		$sheet->setCellValue('O1', 'Bank Name');
		$sheet->setCellValue('P1', 'Account Number');
		$sheet->setCellValue('Q1', 'IFSC Code');
		$sheet->setCellValue('R1', 'Amount');

		// ... Add more data as needed ...
		$row = 2; // Start from row 2
		foreach ($data as $result) {

			if ($result['year'] == 1) {
				$year = '2017-18';
			} elseif ($result['year'] == 2) {
				$year = '2018-19';
			} elseif ($result['year'] == 3) {
				$year = '2019-20';
			} elseif ($result['year'] == 4) {
				$year = '2020-21';
			} elseif ($result['year'] == 5) {
				$year = '2021-22';
			}

			if ($result['season'] == 1) {
				$season = 'kharif';
			} elseif ($result['season'] == 2) {
				$season = 'Rabi';
			}
			$sheet->setCellValue('A' . $row, strtoupper($result['district_name']));
			$sheet->setCellValue('B' . $row, strtoupper($result['block_name']));
			$sheet->setCellValue('C' . $row, $year);
			$sheet->setCellValue('D' . $row, strtoupper($season));
			$sheet->setCellValue('E' . $row, strtoupper($result['gp']));
			$sheet->setCellValue('F' . $row, strtoupper($result['village']));
			$sheet->setCellValue('G' . $row, strtoupper($result['name']));
			$sheet->setCellValue('H' . $row, strtoupper($result['spouse_name']));
			$sheet->setCellValue('I' . $row, strtoupper($result['gender']));
			$sheet->setCellValue('J' . $row, strtoupper($result['caste']));
			$sheet->setCellValue('K' . $row, $result['phone_no']);
			$sheet->setCellValue('L' . $row, $result['aadhar_no']);
			$sheet->setCellValue('M' . $row, $result['year_support']);
			$sheet->setCellValue('N' . $row, $result['area_hectare']);
			$sheet->setCellValue('O' . $row, $result['account_no']);
			$sheet->setCellValue('P' . $row, $result['ifsc']);
			$sheet->setCellValue('Q' . $row, $result['amount']);
			$sheet->setCellValue('R' . $row, $result['phase']);
			$row++;
		}
		// Create a new Xlsx Writer instance
		$writer = new Xlsx($spreadsheet);

		// Set the file name for the downloaded Excel file
		$filename = 'data.xlsx';

		// Set the appropriate headers for the HTTP response
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		// Write the Spreadsheet data to the response body
		$writer->save('php://output');

		// Stop the script execution
		exit();
	}

	public function searchall()
	{
		$requestData = $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		$filter_data = array(
			'filter_district' => $requestData['searchBydistrictId'],
			'filter_block' => $requestData['searchByblockId'],
			'filter_year' => $requestData['searchByYear'],
			'filter_season' => $requestData['searchBySeason'],
			'filter_search' => $requestData['search']['value'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);

		//$totalFiltered = $this->incentiveModel->getTotal($filter_data);
		$filteredData = $this->incentivemainModel->getAllsearch($filter_data);

		//printr($filteredData);
		$datatable = array();
		foreach ($filteredData as $result) {

			if ($result->year == 1) {
				$year = '2017-18';
			} elseif ($result->year == 2) {
				$year = '2018-19';
			} elseif ($result->year == 3) {
				$year = '2019-20';
			} elseif ($result->year == 4) {
				$year = '2020-21';
			} elseif ($result->year == 5) {
				$year = '2021-22';
			}

			if ($result->season == 1) {
				$season = 'kharif';
			} elseif ($result->season == 2) {
				$season = 'Rabi';
			}

			// $action  = '<div class="btn-group btn-group-sm pull-right">';
			// $action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('incentive/view/'.$result->id).'"><i class="fa fa-eye"></i></a>';
			// $action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('incentivemain/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			// $action .= '</div>';

			$datatable[] = array(
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				strtoupper($result->district_name),
				strtoupper($result->block_name),
				$year,
				strtoupper($season),
				strtoupper($result->gp),
				strtoupper($result->village),
				strtoupper($result->name),
				strtoupper($result->spouse_name),
				strtoupper($result->gender),
				strtoupper($result->caste),
				$result->phone_no,
				$result->aadhar_no,
				$result->year_support,
				$result->area_hectare,
				strtoupper($result->bank_name),
				$result->account_no,
				$result->ifsc,
				$result->amount,
				$result->phase,

			);
		}
		$json_data = array(
			"draw"            => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $datatable
		);

		return $this->response->setContentType('application/json')
			->setJSON($json_data);
	}

	protected function filterOptionsMain(&$data)
	{

		$data['years'] = getAllYears();
		$data['months'] = getAllMonths();
		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
		if (isset($data['district_id']) && $data['district_id']) {
			$blockModel = new BlockModel();
			$data['blocks'] = $blockModel->where(['district_id' => $data['district_id']])->findAll();
		}
		//print_r($data['blocks']); exit;

		$data['seasons'] = [
			1 => 'kharif', 2 => 'rabi'
		];
		// echo "<pre>";
		// print_r($data); exit;
		if ($this->request->getGet('year')) {
			$data['year_id'] = $this->request->getGet('year');
		} else {
			$data['year_id'] = getCurrentYearId();
		}
		if ($this->request->getGet('month')) {
			$data['month_id'] = $this->request->getGet('month');
		} else {
			$data['month_id'] = getCurrentMonthId();
		}
		if ($this->request->getGet('season')) {
			$data['season'] = $this->request->getGet('season');
		} else {
			$data['season'] = getCurrentSeason();
		}

		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
		} else {
			$data['district_id'] = 0;
		}
		$this->template->add_package(array('datatable', 'select2'), true);
		$data['filter_panel'] = view('Admin\Incentive\Views\filter', $data);
	}

	public function downloadExcelFile()
	{

		$user  = service('user');
		$userName = $user->username;
		$filePath =  theme_url('assets/farmerin.xlsx');
		$parsedUrl = parse_url($filePath);
		$path = $parsedUrl['path'];
		$filename = basename($path);
		$fileDownload = $userName . '.xlsx';
		helper('download');

		// Set the appropriate headers to force download and specify the file name
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $fileDownload . '"');

		readfile($filePath);
		exit();
	}

	public function addform()
	{
		$this->template->add_package(array('select2'), true);
		$user_upload =  $this->session->get('user')->id;
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		$data['text_form'] = $this->uri->getSegment(4) ? "INCENTIVE EDIT" : "INCENTIVE ADD";
		$data['cancel'] = admin_url('incentive');
		$data['years'] = getAllYears();
		$data['months'] = getAllMonths();
		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
		if (isset($data['district_id']) && $data['district_id']) {
			$blockModel = new BlockModel();
			$data['blocks'] = $blockModel->where(['district_id' => $data['district_id']])->findAll();
		}

		$data['seasons'] = [
			'kharif', 'rabi'
		];
		$data['msgclass'] = '';
		if ($this->request->getMethod(1) === 'POST') {

			//printr($file_pdf); exit;
			$check['district_id'] = $_POST['district_id'];
			$check['block_id'] = $_POST['block_id'];
			$check['year'] = $_POST['year'];
			$check['season'] = $_POST['season'];

			$checkedData = $this->incentiveModel->getcheckExsists($check);

			// if ($checkedData > 0) {
			// 	$this->session->setFlashdata('errorupload', 'Data Already Exists');
			// 	$data['msgclass'] = 'bg-danger';
			// 	return redirect()->to(base_url('admin/incentive/addform'));
			// }

			$checkk['filter_district'] = $_POST['district_id'];
			$checkk['filter_block'] = $_POST['block_id'];
			$checkk['filter_year'] = $_POST['year'];
			$checkk['filter_season'] = $_POST['season'];

			$checkedDatablock = $this->validateForm($checkk);
			if ($checkedDatablock) {
				$this->session->setFlashdata('errorupload', 'Please Verify The Previous Phase Data');
				$data['msgclass'] = 'bg-danger';
				return redirect()->to(base_url('admin/incentive/addform'));
			}


			if (isset($_FILES["file"]["name"])) {

				$file_pdf = $this->request->getFile('pdf');
				$file_pdf->move(DIR_UPLOAD . 'farmerincentive', $file_pdf->getName());
				//for pdf upload code
				//for excel upload
				$fileName = $_FILES["file"]["tmp_name"];
				$reader = IOFactory::createReader('Xlsx');
				$reader->setReadDataOnly(true);
				$spreadsheet = $reader->load($fileName);
				$sheet = $spreadsheet->getActiveSheet();

				// Get the highest column index (e.g., 'A', 'B', 'C', etc.)
				$highestColumn = $sheet->getHighestColumn();

				// Convert the highest column index to a numeric value
				$columnCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
				$checkColumn = $this->countExcelHeaders();
				if ($checkColumn == $columnCount) {
					$activesheet = $spreadsheet->getSheet(0);
					$row_data = $activesheet->toArray();
					// dd($row_data);
					if (!empty($row_data[1])) {


						$result_main_phase = $this->incentiveModel->phaseDataCheck($checkk);

						$phase = '';
						if (empty($result_main_phase)) {
							$phase = 1;
						} else {
							$result_main_phase_result = $result_main_phase->phase;
							$phase = $result_main_phase_result + 1;
						}

						$main_incetive_data = array(
							'district_id' => $check['district_id'],
							'block_id' => $check['block_id'],
							'year' => $check['year'],
							'season' => $check['season'],
							'pdf' => $file_pdf->getName(),
							'created_by' => $user_upload,
							'phase' => $phase,

						);



						$result_main = $this->incentiveModel->addInceitive_main($main_incetive_data);
						if ($_FILES["file"]["size"] > 0) {

							foreach ($row_data  as $key => $column) {
								$filteredColumn = array_filter($column);
								if ($key >= 1) {
									$aadhar_no = preg_replace("/[^0-9]/", "", $column[7]);
									$account_no = preg_replace("/[^0-9]/", "", $column[11]);
									$datacsv[] = array(
										'incetive_id' => $result_main,
										'gp' => $column[0],
										'village' => $column[1],
										'name' => $column[2],
										'spouse_name' => $column[3],
										'gender' => strtolower($column[4]),
										'caste' => strtolower($column[5]),
										'phone_no' => $column[6],
										'aadhar_no' => $aadhar_no,
										'year_support' => $column[8],
										'area_hectare' => $column[9],
										'bank_name' => $column[10],
										'account_no' => trim($account_no),
										'ifsc' =>  $column[12],
										'amount' => $column[13],
										'uploaded_by' => $user_upload
									);
								}
							}
							//dd($datacsv); 
							$res = $this->incentiveModel->addInceitive($datacsv);
							$this->session->setFlashdata('errorupload', 'Data Uploaded successfully');
							$data['msgclass'] = 'bg-success';
							return redirect()->to(base_url('admin/incentive'));
						}
					} else {

						$this->session->setFlashdata('errorupload', 'Don\'t upload Blank Excel');
						$data['msgclass'] = 'bg-danger';
						return redirect()->to(base_url('admin/incentive/addform'));
					}
				} else {
					$this->session->setFlashdata('errorupload', 'Invalid FIle Please Use the same File as You Downloaded');
					$data['msgclass'] = 'bg-danger';
					return redirect()->to(base_url('admin/incentive/addform'));
				}
			}
		}

		return $this->template->view('Admin\Incentive\Views\form', $data);
	}

	protected function countExcelHeaders()
	{
		// Set the file path to the Excel file
		$filePath =  FCPATH . 'themes/admin/assets/farmerin.xlsx';

		$reader = IOFactory::createReader('Xlsx');
		$spreadsheet = $reader->load($filePath);

		// Get the first sheet in the Excel file
		$sheet = $spreadsheet->getActiveSheet();

		// Get the highest column index (e.g., 'A', 'B', 'C', etc.)
		$highestColumn = $sheet->getHighestColumn();

		// Convert the highest column index to a numeric value
		$columnCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

		return $columnCount;
	}

	public function view()
	{
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		return $this->getList();
	}

	public function add()
	{
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		if ($this->request->getMethod(1) === 'POST') {
		}
		$this->getForm();
	}

	public function edit()
	{
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		if ($this->request->getMethod(1) === 'POST') {
			$id = $this->uri->getSegment(4);
			// echo "<pre>";
			// print_r($_POST); exit;
			$this->incentiveModel->update($id, $this->request->getPost());
			$this->session->setFlashdata('message', 'Incentive Updated Successfully.');

			return redirect()->to(base_url('admin/incentive'));
		}
		$this->getForm();
	}

	public function delete()
	{
		//$deleteid=$this->uri->getSegment(4); 
		if ($this->request->getPost('selected')) {
			$selected = $this->request->getPost('selected');
		} else {
			$selected = (array) $this->uri->getSegment(4);
		}
		$this->incentiveModel->deleteincentive($selected);

		//$this->slugModel->whereIn('route_id', $selected)->delete();

		$this->session->setFlashdata('message', 'Incentive deleted Successfully.');
		return redirect()->to(base_url('admin/incentive'));
	}

	protected function getList()
	{

		$data['mainincetiveid'] = $this->uri->getSegment(4);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$this->template->add_package(array('datatable', 'select2'), true);
		$data['addform'] = admin_url('incentive/addform');
		$data['delete'] = admin_url('incentive/delete');

		$data['heading_title'] = lang('Incentive.heading_title');

		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');

		$data['button_add'] = lang('Incentive.button_add');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');
		$data['datatable_url'] = admin_url('incentive/search');
		if (isset($this->error['warning'])) {
			$data['error'] 	= $this->error['warning'];
		}
		$this->filterOptions($data);

		return $this->template->view('Admin\Incentive\Views\index', $data);
	}

	public function search()
	{
		$requestData = $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		$filter_data = array(
			'mainincetiveid' => $requestData['mainincetiveid'],
			'filter_search' => $requestData['search']['value'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$filteredData = $this->incentiveModel->getAll($filter_data);


		$datatable = array();
		foreach ($filteredData as $result) {
			$error = 'false';
			//validation rules
			// if (!preg_match('/^[0-9]{10}+$/', $result->phone_no) || empty($result->phone_no)) {
			// 	$error  = 'true';
			// } else if (!preg_match('/^[0-9]{12}+$/', $result->aadhar_no) || empty($result->aadhar_no)) {
			// 	$error  = 'true';
			// } 
			if (empty($result->gp) || empty($result->village) || empty($result->name)) {
				$error  = 'true';
			} else if ($result->area_hectare == 0 || $result->area_hectare > 9.99 || empty($result->area_hectare)) {
				$error  = 'true';
			} else if (!preg_match('/^[0-9]{9,18}$/', $result->account_no) || empty($result->account_no)) {
				$error  = 'true';
			} else if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $result->ifsc) || empty($result->ifsc)) {
				$error  = 'true';
			}

			$action  = '<div class="btn-group btn-group-sm pull-right">';
			$action .= 		'<a class="btn btn-sm btn-primary" href="' . admin_url('incentive/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
			//	$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('incentive/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';

			$datatable[] = array(
				$error,
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				$result->name,
				$result->spouse_name,
				strtoupper($result->gender),
				strtoupper($result->caste),
				$action
			);
		}
		$json_data = array(
			"draw"            => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $datatable
		);

		return $this->response->setContentType('application/json')
			->setJSON($json_data);
	}

	protected function getForm()
	{

		$this->template->add_package(array('select2'), true);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$data['heading_title'] 	= lang('Incentive.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Incentive Edit" : "Incentive Add";
		$data['cancel'] = admin_url('incentive');

		if (isset($this->error['warning'])) {
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$incentive_info = $this->incentiveModel->find($this->uri->getSegment(4));
		}

		foreach ($this->incentiveModel->getFieldNames('detailed_incentive_data') as $field) {
			if ($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if (isset($incentive_info->{$field}) && $incentive_info->{$field}) {
				$data[$field] = html_entity_decode($incentive_info->{$field}, ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}

		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
		if (isset($data['district_id']) && $data['district_id']) {
			$blockModel = new BlockModel();
			$data['blocks'] = $blockModel->where(['district_id' => $data['district_id']])->findAll();
		}
		echo $this->template->view('Admin\Incentive\Views\incentiveform', $data);
	}

	public function ajaxverify()
	{
		$filter_data = array(
			'mainincetiveid' => $_POST['checkboxValue'],

		);
		$filteredData = $this->incentiveModel->getAll($filter_data);
		$error = 0;
		foreach ($filteredData as $result) {
			if ($this->checkError($result) == 'true') {
				$error = 1;
				break;
			}
		}
		echo $error;
	}

	public function ajaxverifyupdate()
	{
		$id = $_POST['checkboxValue'];
		// $this->incentiveModel->update($id,$id);
		$row = $this->incentivemainModel->find($id);
		$row->verify = 1;
		$updateverify = true;
		if ($this->incentivemainModel->save($row)) {
			echo $updateverify;
		}
	}

	protected function validateForm($filter_data)
	{
		//printr($filter_data); exit;
		$filteredData = $this->incentivemainModel->getAllCheckblockwise($filter_data);
		// 

		// $filtered_Data = $this->incentiveModel->getAll($filteredData->id);
		// printr($filtered_Data); exit;


		foreach ($filteredData as $result) {
			if ($result['verify'] == 0) {
				return true;
			}
		}
	}

	protected function checkError($result)
	{
		$error = 'false';
		//validation rules
		// if (!preg_match('/^[0-9]{10}+$/', $result->phone_no) || empty($result->phone_no)) {
		// 	$error  = 'true';
		// } else if (!preg_match('/^[0-9]{12}+$/', $result->aadhar_no) || empty($result->aadhar_no)) {
		// 	$error  = 'true';
		// }
		if (empty($result->gp) || empty($result->village) || empty($result->name) || empty($result->gender) || empty($result->year_support)) {
			$error  = 'true';
		} else if ($result->area_hectare == 0 || $result->area_hectare > 9.99 || empty($result->area_hectare)) {
			$error  = 'true';
		} else if (!preg_match('/^[0-9]{9,18}$/', $result->account_no) || empty($result->account_no)) {
			$error  = 'true';
		} else if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $result->ifsc) || empty($result->ifsc)) {
			$error  = 'true';
		}
		return $error;
	}

	protected function filterOptions(&$data)
	{

		$data['years'] = getAllYears();
		$data['months'] = getAllMonths();
		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
		if (isset($data['district_id']) && $data['district_id']) {
			$blockModel = new BlockModel();
			$data['blocks'] = $blockModel->where(['district_id' => $data['district_id']])->findAll();
		}
		//print_r($data['blocks']); exit;

		$data['seasons'] = [
			1 => 'kharif', 2 => 'rabi'
		];
		// echo "<pre>";
		// print_r($data); exit;
		if ($this->request->getGet('year')) {
			$data['year_id'] = $this->request->getGet('year');
		} else {
			$data['year_id'] = getCurrentYearId();
		}
		if ($this->request->getGet('month')) {
			$data['month_id'] = $this->request->getGet('month');
		} else {
			$data['month_id'] = getCurrentMonthId();
		}
		if ($this->request->getGet('season')) {
			$data['season'] = $this->request->getGet('season');
		} else {
			$data['season'] = getCurrentSeason();
		}

		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
		} else {
			$data['district_id'] = 0;
		}
		$this->template->add_package(array('datatable', 'select2'), true);
		$data['filter_panel'] = view('Admin\Incentive\Views\filter', $data);
	}

	public function uploadStatus()
	{
		$data['districts'] = (new DistrictModel())->asArray()->findAll();
		// Retrieve the districts from the DistrictModel and store them in $data['districts']

		$data['selectedYear'] = 1;
		// Set the default value for the selected year to 1

		if ($this->request->getGet('year')) {
			$data['selectedYear'] = $this->request->getGet('year');
		}
		// If the 'year' parameter is present in the request, update the selected year with its value

		$data['year_id'] = 1;
		// Set the default value for the year ID to 1

		if ($this->request->getGet('year')) {
			$data['year_id'] = $this->request->getGet('year');
		}
		// If the 'year' parameter is present in the request, update the year ID with its value

		$filter = [
			'year' => $data['year_id'],
			'district_id' => 0
		];
		// Create a filter array with the year ID and default district ID as 0

		if ($this->request->getGet('district_id')) {
			$filter['district_id'] = $this->request->getGet('district_id');
		}
		// If the 'district_id' parameter is present in the request, update the district ID in the filter

		$filteredData = $this->incentivemainModel->FarmerCheckstatus($filter);
		// Retrieve filtered data by calling the FarmerCheckstatus() method on the incentivemainModel, passing the filter
		$data['district_id'] = '';
		// Set the initial value of district_id to an empty string
		
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
		}
		// If the 'district_id' parameter is present in the request, update the district_id value with its value
		
		$data['farmerData'] = [];
		// Initialize an empty array to store the farmer data
		
		foreach ($filteredData as $filteredDatas) {
			$year = 0;
			// Initialize the year variable with a default value of 0
		
			if ($filteredDatas['year'] == 1) {
				$year = '2017-18';
			} elseif ($filteredDatas['year'] == 2) {
				$year = '2018-19';
			} elseif ($filteredDatas['year'] == 3) {
				$year = '2019-20';
			} elseif ($filteredDatas['year'] == 4) {
				$year = '2020-21';
			} elseif ($filteredDatas['year'] == 5) {
				$year = '2021-22';
			}
			// Assign a specific string value to $year based on the value of the 'year' field in $filteredDatas
		
			$season = 0;
			// Initialize the season variable with a default value of 0
		
			if ($filteredDatas['season'] == 1) {
				$season = 'kharif';
			} elseif ($filteredDatas['season'] == 2) {
				$season = 'Rabi';
			}
			// Assign a specific string value to $season based on the value of the 'season' field in $filteredDatas
		
			$data['farmerData'][] = [
				'district_id' => $filteredDatas['district_id'],
				'district_name' => $filteredDatas['district'],
				'block_id' => $filteredDatas['block_id'],
				'block_name' => $filteredDatas['block_name'],
				'incentiveid' => $filteredDatas['incentiveid'],
				'year' => $year,
				'season' => $season,
			];
			// Add an associative array with various fields and their corresponding values to the $data['farmerData'] array
		}
		;
		return $this->template->view('Admin\Incentive\Views\upload_status', $data);
	}
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */