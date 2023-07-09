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
	protected function getList() {
		$this->template->add_package(array('datatable', 'select2','uploader'), true);

		$data['add'] = admin_url('areacoverage/gp/add');
		$data['delete'] = admin_url('grampanchayat/delete');

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

        $dates = $this->getWeekDates();

        $data['from_date'] = $dates[0];
        $data['to_date'] = $dates[1];

		return $this->template->view('Admin\CropCoverage\Views\areacoverage', $data);
	}

	public function download() {
	    
	}

	public function getCurrentYearDates() {

		$kharif_start_month = getMonthById((int)$this->settings->kharif_start_month);
        $kharif_end_month = getMonthById((int)$this->settings->kharif_end_month);

        $rabi_start_month = getMonthById((int)$this->settings->rabi_start_month);
        $rabi_end_month = getMonthById((int)$this->settings->rabi_end_month);

        $given_date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

        $kharif_start_month_number = $kharif_start_month['number'];
        $kharif_end_month_number = $kharif_end_month['number'];
        $rabi_start_month_number = $rabi_start_month['number'];
        $rabi_end_month_number = $rabi_end_month['number'];
        if ($given_date->format('n') >= $kharif_start_month_number
            && $given_date->format('n') <= $kharif_end_month_number) {
            $current_season = 'Kharif';
            $season_start_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-'.$kharif_start_month_number.'-01');
            $season_end_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-'.$kharif_end_month_number.'-30');
        } elseif ($given_date->format('n') >= $rabi_start_month_number
            || $given_date->format('n') <= $rabi_end_month_number) {
            $current_season = 'Rabi';
            $season_start_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-'.$rabi_start_month_number.'-01');
            $season_end_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-'.$rabi_end_month_number.'-01');
            if ($given_date->format('n') <= 3) {
                $season_start_date->modify('-1 year');
                $season_end_date->modify('-1 year');
            }
        }

        return [
            'current_season'=>$current_season,
            'start_date'=>$season_start_date,
            'end_date'=>$season_end_date,
        ];

    }

    private function getWeekDates(){

	    $dates = $this->getCurrentYearDates();

        $start = $dates['start_date'];
        $end = $dates['end_date'];
        $week_start = $this->settings->start_week;
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

        foreach ($output as $dates) {
            $today = strtotime('today');
            if($today >= strtotime($dates[0]) && $today <= strtotime($dates[1])){
                return $dates;
            }
        }

        return false;
    }

}