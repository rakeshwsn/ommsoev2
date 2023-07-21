<?php
namespace Admin\CropCoverage\Controllers;

use Admin\Common\Models\YearModel;
use Admin\CropCoverage\Models\AreaCoverageModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\CropsModel;
use Config\Url;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
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
                $action = '';
                if(strtotime($data['to_date'])<=strtotime('today')){
                    $action = admin_url('areacoverage/edit?id='.$block->cc_id);
                }
                $data['blocks'][] = [
                    'week' => date('d F',strtotime($block->start_date)).'-'.date('d F',strtotime($block->end_date)) ,
                    'gp' => $block->gp,
                    'farmers_covered' => $block->farmers_covered,
                    'total_area' => $block->smi+$block->lt+$block->ls+$block->fc_area,
                    'action' => $action,
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

        $validation = $sheet->getCell('F5')
            ->getDataValidation();
        $validation->setType(DataValidation::TYPE_DECIMAL );
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Only numbers/decimals allowed!');
        $validation->setPromptTitle('Allowed input');
        $validation->setPrompt('Only numbers greater than or equal to 0 are allowed.');
        $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
        $validation->setFormula1("=0");
        $validation->setSqref("F5:AB$row");

        $cells = "F5:S$row";
        $sheet->getStyle($cells)
            ->getProtection()
            ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        $cells = "V5:AB$row";
        $sheet->getStyle($cells)
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
            $excel_from_date = $row_data[0][22];

            $exists = $acModel
                ->where('start_date',$from_date)
                ->where('block_id',$this->user->block_id)
                ->where('season',$current['season'])
                ->where('year_id',getCurrentYearId())
                ->first();
//            $exists = false;

            //gp belongs to the block
            $gp_cell = $row_data[4][1];
            $gp = (new GrampanchayatModel())->find($gp_cell);
            $gp_belongs = $gp->block_id==$this->user->block_id;

            //validation
            if(!isset($row_data[0][22])){
                return $this->response->setJSON([
                    'status'=>false,
                    'message'=>'Invalid file. Please download the file from here and upload again.'
                ]);
            } else if(strtotime($from_date)!=strtotime($excel_from_date)){
                return $this->response->setJSON([
                    'status'=>false,
                    'message'=>'Invalid week dates. Please download the latest file and upload again.'
                ]);
            } else if($exists){
                return $this->response->setJSON([
                    'status'=>false,
                    'message'=>'This week data is already uploaded.'
                ]);
            } else if(!$gp_belongs){
                return $this->response->setJSON([
                    'status'=>false,
                    'message'=>'The GP dont belong to your block. Please download the file and try again.'
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

        return $this->response->setJSON([
            'status'=>true,
            'message'=>'Upload successful.',
            'url' => admin_url('areacoverage')
        ]);
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

    public function edit() {
        $cc_id = $this->request->getGet('id');

        $dates = $this->getWeekDates();

        $to_date = $dates[1];

        $data['show_form'] = false;
        if(strtotime($to_date)>=strtotime('today')){
            $data['show_form'] = true;
        }

        if($this->request->getMethod(1)=='POST'){
            $master = [
                'farmers_covered' => $this->request->getPost('crop_coverage')['farmers_covered'],
            ];
            $this->areacoveragemodel->update($cc_id,$master);

            $nursery = [
                'crop_coverage_id' => $cc_id,
                'nursery_raised' => $this->request->getPost('nursery')['nursery_raised'],
                'balance_smi' => $this->request->getPost('nursery')['balance_smi'],
                'balance_lt' => $this->request->getPost('nursery')['balance_lt'],
            ];

            $this->areacoveragemodel->deleteNursery($cc_id);
            $this->areacoveragemodel->addNursery($nursery);

            $cropPractices = $this->areacoveragemodel->getCropPractices();

            $areas = [];

            foreach ($cropPractices as $crop_id => $practices) {
                $_areas = [
                    'crop_coverage_id' => $cc_id,
                    'crop_id' => $crop_id,
                ];
                foreach ($practices as $practice) {
                    $_areas[$practice] = $this->request->getPost('area')[$crop_id][$practice];
                }
                $areas[] = $_areas;
            }

            $this->areacoveragemodel->deleteArea($cc_id);
            $this->areacoveragemodel->addArea($areas);

            //follow up crops

            $crops = (new CropsModel())->findAll();
            $fCrop = [];
            foreach ($crops as $crop) {
                $fCrop[] = [
                    'crop_coverage_id' => $cc_id,
                    'crop_id' => $crop->id,
                    'area' => $this->request->getPost('fup')[$crop->id],
                ];
            }
            $this->areacoveragemodel->deleteFupCrops($cc_id);
            $this->areacoveragemodel->addFupCrops($fCrop);

            return redirect()->to(admin_url('areacoverage'))->with('message','Area coverage data updated.');
        }

        return $this->getForm();
    }

    protected function getForm(){
        $cc_id = $this->request->getGet('id');

        $cc_info = $this->areacoveragemodel->find($cc_id);

        $dates = $this->getWeekDates();

        $to_date = $dates[1];

        $data['show_form'] = false;
        if(strtotime($to_date)>=strtotime('today')){
            $data['show_form'] = true;
        }

        $data['district'] = (new DistrictModel())->find($cc_info->district_id)->name;
        $data['block'] = (new BlockModel())->find($cc_info->block_id)->name;
        $data['gp'] = (new GrampanchayatModel())->find($cc_info->gp_id)->name;
        $data['year'] = (new YearModel())->find($cc_info->year_id)->name;
        $data['season'] = $cc_info->season;
        $data['date_added'] = ymdToDmy($cc_info->created_at);
        $data['start_date'] = ymdToDmy($cc_info->start_date);
        $data['end_date'] = ymdToDmy($cc_info->end_date);

        $cropPrtcArea = $this->areacoveragemodel->getPracticeArea($cc_id);

        $data['crop_coverage']['farmers_covered'] = $cc_info->farmers_covered;
        $data['nursery_info'] = $this->areacoveragemodel->getNursery($cc_id);

        $data['crops'] = [];
        $smi = $lt = $ls = 0;
        foreach ($cropPrtcArea as $area) {
            $practices = [];
            foreach ($cropPrtcArea as $p) {
                if($area['crop_id']==$p['crop_id']){
                    $practices[strtolower($p['practice'])] = [
                        'area' => $p['area'],
                        'status' => $p['status']
                    ];
                }
            }

            $data['crops'][$area['crop_id']] = [
                'crop' => $area['crop'],
                'crop_id' => $area['crop_id'],
                'practices' => $practices,
            ];
        }
        $practices = [];
        foreach ($cropPrtcArea as $area) {
            if(strtolower($area['practice'])=='smi'){
                $smi += $area['area'];
            }
            if(strtolower($area['practice'])=='lt'){
                $lt += $area['area'];
            }
            if(strtolower($area['practice'])=='ls'){
                $ls += $area['area'];
            }
        }
        //cacl total
        foreach (['smi','lt','ls'] as $practice){
            $practices[$practice] = [
                'area' => $$practice,
                'status' => 0
            ];
        }
        $data['crops'][99] = [
            'crop' => 'Total',
            'crop_id' => 0,
            'practices' => $practices,
        ];

        //fup
        $data['fups'] = $this->areacoveragemodel->getFupCrops($cc_id);
        $area = 0;
        foreach ($data['fups'] as $fup) {
            $area += $fup['area'];
        }
        $data['fups_total'] = $area;

        return $this->template->view('Admin\CropCoverage\Views\areacoverage_edit', $data);
    }

}