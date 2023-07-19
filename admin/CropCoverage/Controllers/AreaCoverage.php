<?php
namespace Admin\CropCoverage\Controllers;

use Admin\CropCoverage\Models\AreaCoverageModel;
use Admin\CropCoverage\Models\YearModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\CropsModel;
use Config\Url;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use Admin\CropCoverage\Models\AreaCoverageModel;
class AreaCoverage extends AdminController
{
	private $error = array();
	function __construct() {
        $this->cropsmodel = new CropsModel();
        $this->districtModel = new DistrictModel();
        $this->areacoveragemodel = new AreaCoverageModel();
    }
	public function index()
	{
		$this->template->set_meta_title(lang('Seasons Data.heading_title'));

		return $this->getList();

	}
	protected function getList() {
		$this->template->add_package(array('datatable', 'select2','uploader','jquery_loading'), true);

		$data['add'] = admin_url('areacoverage/gp/add');
		$data['delete'] = admin_url('grampanchayat/delete');
		$data['download_url'] = admin_url('areacoverage/download');

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

		$districtModel = new DistrictModel();
		$data['districts'] = $districtModel->getAll();

        $dates = $this->getWeekDates();

        $data['from_date'] = $dates[0];
        $data['to_date'] = $dates[1];
        $data['upload_url'] = Url::areaCoverageUpload;

        $view = 'areacoverage_block';
        if($this->user->block_id){
            $filter = [
                'block_id' => $this->user->block_id,
                'year_id' => getCurrentYearId(),
                'season' => getCurrentSeason()
            ];

            $blocks = $this->areacoveragemodel->getAreaCoverage($filter);

            $data['blocks'] = [];
            foreach ($blocks as $block) {
                $data['blocks'][] = [
                    'week' => date('d F',strtotime($block->start_date)).'-'.date('d F',strtotime($block->end_date)) ,
                    'farmers_covered' => $block->farmers_covered,
                    'total_area' => $block->smi+$block->lt+$block->ls+$block->fc_area,
                    'action' => '',
                ];
            }

            $view = 'areacoverage_block';
        } else if($this->user->district_id){
            $filter = [
                'district_id' => $this->user->district_id,
                'year_id' => getCurrentYearId(),
                'season' => getCurrentSeason()
            ];

            $districts = $this->areacoveragemodel->getAreaCoverage($filter);
            $view = 'areacoverage_district';
        }

		return $this->template->view('Admin\CropCoverage\Views\\'.$view, $data);
	}

	public function download() {

        $dates = $this->getWeekDates();

        $data['from_date'] = $dates[0];
        $data['to_date'] = $dates[1];

        $reader = IOFactory::createReader('Xlsx');
        $template_file = DIR_TEMPLATE.'area_coverage.xlsx';

        $spreadsheet = $reader->load($template_file);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('W1',$data['from_date']);
        $sheet->setCellValue('Z1',$data['to_date']);
        $year_text = getCurrentYear();
        $sheet->setCellValue('F1','District wise weekly Crop Progress under OMM during '.$year_text);

        $current_season = $this->getCurrentYearDates()['season'];
        
        $fin_year = getCurrentYear();

        $gps = (new GrampanchayatModel())->getGPsByBlock($this->user->block_id);

        $row = 4;
        foreach ($gps as $key => $gp) {
            $row++;
            $sheet->setCellValue("A$row",$gp->block_id);
            $sheet->setCellValue("B$row",$gp->gp_id);
            $sheet->setCellValue("C$row",($key+1));
            $sheet->setCellValue("D$row",$gp->block);
            $sheet->setCellValue("E$row",$gp->gp);

            $sheet->setCellValue("T$row","=J$row+K$row+L$row");
            $sheet->setCellValue("U$row","=SUM(M$row:S$row)");
            $sheet->setCellValue("AC$row","=SUM(V$row:AB$row)");
            $sheet->setCellValue("AD$row","=SUM(T$row:AB$row)");
        }

        // Set read-only mode to prevent adding new rows when reading
        $protection = $spreadsheet->getActiveSheet()->getProtection();
        $protection->setAlgorithm(Protection::ALGORITHM_SHA_512);
        $protection->setPassword('PhpSpreadsheet');
        $protection->setSheet(true);
        $protection->setSort(false);
        $protection->setInsertRows(false);
        $protection->setFormatCells(false);

        $sheet->getStyle("F5:S$row")
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("V5:AB$row")
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

//        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="area_coverage_' . $this->user->username . '_' . $current_season . '_' . $fin_year . '_' . date('Y_m_d_His') . '.xlsx');

        $writer->save("php://output");
        exit;
	}

    public function upload() {
        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]',
                'max_size[file,1024]',
                'ext_in[file,xlsx]',
            ]
        ]);

        if (!$input) {
            return $this->response->setJSON([
                'status'=>false,
                'message'=>'Invalid file',
                'errors'=>$this->validator->getErrors()
            ]);
        } else {
            $acModel = new AreaCoverageModel();
            $file = $this->request->getFile('file');

            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($file);

            $activesheet = $spreadsheet->getSheet(0);

            $row_data = $activesheet->toArray();

            $dates = $this->getWeekDates();
            $current = $this->getCurrentYearDates();

            $from_date = $dates[0];
            $to_date = $dates[1];

            $exists = $acModel
                ->where('start_date',$from_date)
                ->where('block_id',$this->user->block_id)
                ->where('season',$current['season'])
                ->where('year_id',getCurrentYearId())
                ->first();
//            $exists = false;

            if(strtotime($from_date)!=strtotime($row_data[0][22])){
                return $this->response->setJSON([
                    'status'=>false,
                    'message'=>'Invalid week dates. Please download the latest file and upload again.'
                ]);
            } else if($exists){
                return $this->response->setJSON([
                    'status'=>false,
                    'message'=>'This week data is already uploaded.'
                ]);
            } else {
                $crops = (new CropsModel())->findAll();

                foreach ($row_data as $gp) {
                    //only rows with gp_id
                    if(is_numeric($gp[0])){
                        $master = [
                            'start_date' => $from_date,
                            'end_date' => $to_date,
                            'district_id' => $this->user->district_id,
                            'year_id' => getCurrentYearId(),
                            'season' => $current['season'],
                            'block_id' => $gp[0],
                            'gp_id' => $gp[1],
                            'farmers_covered' => $gp[5],
                        ];

//                        $ac_crop_coverage_id = 0;
                        $ac_crop_coverage_id = $acModel->insert($master);

                        $col=5;
                        $nursery = [
                            'crop_coverage_id' => $ac_crop_coverage_id,
                            'nursery_raised' => $gp[++$col],
                            'balance_smi' => $gp[++$col],
                            'balance_lt' => $gp[++$col],
                        ];

                        $acModel->addNursery($nursery);

                        $cropPractices = $acModel->getCropPractices();

                        $areas = [];

                        foreach ($cropPractices as $crop_id => $practices) {
                            $_areas = [
                                'crop_coverage_id' => $ac_crop_coverage_id,
                                'crop_id' => $crop_id,
                            ];
                            foreach ($practices as $practice) {
                                $_areas[$practice] = $gp[++$col];
                            }
                            $areas[] = $_areas;
                        }

                        $acModel->addArea($areas);

                        //follow up crops

                        $col+=2;
                        $fCrop = [];
                        foreach ($crops as $crop) {
                            $fCrop[] = [
                                'crop_coverage_id' => $ac_crop_coverage_id,
                                'crop_id' => $crop->id,
                                'area' => $gp[++$col],
                            ];
                        }
                        $acModel->addFupCrops($fCrop);

                    }
                }
            }
        }

        return $this->response->setJSON(['status'=>true,'message'=>'Upload successful.']);
	}

    private function getCurrentYearDates() {

		return $this->areacoveragemodel->getCurrentYearDates();

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