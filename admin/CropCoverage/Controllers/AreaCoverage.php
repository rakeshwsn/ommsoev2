<?php
namespace Admin\CropCoverage\Controllers;

use Admin\CropCoverage\Models\YearModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\DistrictModel;

// use Admin\CropCoverage\Models\AreaCoverageModel;
class AreaCoverage extends AdminController
{
	private $error = array();
	function __construct()
	{
		$this->cropsmodel = new CropsModel();
		$this->districtModel = new DistrictModel();
		// $this->areacoveragemodel=new AreaCoverageModel();
	}
	public function index()
	{
		$this->template->set_meta_title(lang('Seasons Data.heading_title'));
		// printr ($_POST);
		// exit;
		if ($this->request->getMethod(1) === 'POST') {

			$this->areacoveragemodel->addAC($this->request->getPost());

			$this->session->setFlashdata('message', 'Area Coverage Saved Successfully.');

		}

		return $this->getList();

	}
	protected function getList()
	{
		
		$this->template->add_package(array('datatable', 'select2','uploader'), true);

		$data['add'] = admin_url('areacoverage/gp/add');
		$data['delete'] = admin_url('grampanchayat/delete');
		$data['datatable_url'] = admin_url('areacoverage/search');

		$data['heading_title'] = lang('Add Area Coverage');

		$data['text_list'] = lang('Grampanchayat.text_list');
		$data['text_no_results'] = lang('Grampanchayat.text_no_results');
		$data['text_confirm'] = lang('Grampanchayat.text_confirm');

		$data['button_add'] = lang('Grampanchayat.button_add');
		$data['button_edit'] = lang('Grampanchayat.button_edit');
		$data['button_delete'] = lang('Grampanchayat.button_delete');

		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array) $this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}
		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();
		

		return $this->template->view('Admin\CropCoverage\Views\areacoverage', $data);
	}
	public function search()
	{
		$requestData = $_REQUEST;
		$totalData = $this->grampanchayatModel->getTotals();
		$totalFiltered = $totalData;

		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'filter_district' => $requestData['district'],
			'filter_block' => $requestData['block'],
			'filter_grampanchayat' => $requestData['grampanchayat'],
			'order' => $requestData['order'][0]['dir'],
			'sort' => $requestData['order'][0]['column'],
			'start' => $requestData['start'],
			'limit' => $requestData['length']
		);
		$totalFiltered = $this->grampanchayatModel->getTotals($filter_data);

		$filteredData = $this->grampanchayatModel->getAll($filter_data);

		$datatable = array();
		foreach ($filteredData as $result) {

			$action = '<div class="btn-group btn-group-sm pull-right">';
			$action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('grampanchayat/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
			$action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('grampanchayat/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';

			$datatable[] = array(
				'<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
				$result->name,
				$result->district,
				$result->block,
				$action
			);

		}
		//printr($datatable);
		$json_data = array(
			"draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
			"recordsTotal" => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data" => $datatable
		);

		return $this->response->setContentType('application/json')
			->setJSON($json_data);

	}

	public function download()
	{
		$start_month = (int)$this->settings->kharif_start_month;
		$end_month = (int)$this->settings->kharif_end_month;

        $current_month = getMonthById(getCurrentMonthId());
        $current_month_id = $current_month['id'];

        //kharif
        if ($current_month_id >= $start_month && $current_month_id <= $end_month) {
            $start_month = getMonthById($start_month);
            $year = date('Y');
            $date = mktime(0,0,0,$start_month['number'],01,$year);

            $start_date = date('Y-m-d',$date);

            $end_month = getMonthById($end_month);
            $date = mktime(0,0,0,$end_month['number'],01,date('Y'));

            $end_date = date('Y-m-d',$date);
        } else {
            $start_month = (int)$this->settings->rabi_start_month;
            $end_month = (int)$this->settings->rabi_end_month;

            $start_month = getMonthById($start_month);
            $date = mktime(0,0,0,$start_month['number'],01,date('Y'));

            $start_date = date('Y-m-d',$date);

            $end_month = getMonthById($end_month);
            $date = mktime(0,0,0,$end_month['number'],01,date('Y'));
            $year = date('Y');
            $date = mktime(0,0,0,$start_month['number'],01,($year+1));

            $end_date = date('Y-m-d',$date);
        }

		$week_start = $this->settings->end_week;
		$start = new \DateTime($start_date);
		$end = new \DateTime($end_date);
		$week_start_index = array_search(strtolower($week_start), array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'));

		$output = array();

		while ($start <= $end) {
			$day_of_week = (int) $start->format('w');
			if ($day_of_week === $week_start_index && $start >= $start && $start <= $end) {
				$output[] = array(
					$start->format('Y-m-d'),
					min($start->modify('+6 days'), $end)->format('Y-m-d')
				);
			}
			$start->modify('+1 day');
		}

		printr($output);
	}

	public function getCurrentYearDates() {

		$kharif_start_month = getMonthById((int)$this->settings->kharif_start_month);
        $kharif_end_month = getMonthById((int)$this->settings->kharif_end_month);

        $rabi_start_month = getMonthById((int)$this->settings->rabi_start_month);
        $rabi_end_month = getMonthById((int)$this->settings->rabi_end_month);

        $given_date = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

        $kharif_start_month_number = $kharif_start_month['number'];
        $kharif_end_month_number = $kharif_end_month['number'];
        $rabi_start_month_number = $rabi_start_month['number'];
        $rabi_end_month_number = $rabi_end_month['number'];
        if ($given_date->format('n') >= $kharif_start_month_number
            && $given_date->format('n') <= $kharif_end_month_number) {
            $current_season = 'Kharif';
            $season_start_date = DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-'.$kharif_start_month_number.'-01');
        } elseif ($given_date->format('n') >= $rabi_start_month_number
            || $given_date->format('n') <= $rabi_end_month_number) {
            $current_season = 'Rabi';
            $season_start_date = DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-'.$rabi_start_month_number.'-01');
            if ($given_date->format('n') <= 3) {
                $season_start_date->modify('-1 year');
            }
        }



    }



}