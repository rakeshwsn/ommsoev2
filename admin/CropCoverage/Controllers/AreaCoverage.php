<?php
namespace Admin\CropCoverage\Controllers;

use Admin\Common\Models\YearModel;
use Admin\CropCoverage\Models\AreaCoverageModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\CropsModel;
use Complex\Exception;
use Config\Url;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use Admin\CropCoverage\Models\AreaCoverageModel;
class AreaCoverage extends AdminController
{
    private $cropsModel;
    private $districtModel;
    private $areacoveragemodel;
    public $colors_ac = [
        'warning',
        'success',
        'danger',
        'secondary',
        'primary',
    ];
    private $error = array();
    function __construct()
    {
        $this->cropsModel = new CropsModel();
        $this->districtModel = new DistrictModel();
        $this->areacoveragemodel = new AreaCoverageModel();
    }
    public function index()
    {

        $this->template->set_meta_title(lang('Seasons Data.heading_title'));

        return $this->getList();
    }

    protected function getList()
    {
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);

        $data['filtered_data_url'] = admin_url('areacoverage/filtered');
        $data['download_url'] = admin_url('areacoverage/download');

        $data['heading_title'] = lang('Add Area Coverage');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        $districtModel = new DistrictModel();
        $data['districts'] = $districtModel->getAll();

        $data['currentDay'] = date('l');

        $data['isActiveDay'] = in_array($data['currentDay'], array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'));

        $dates = $this->areacoveragemodel->getWeekDate();

        $data['from_date'] = '';
        $data['to_date'] = '';
        if ($dates) {
            $data['from_date'] = $dates['start_date'];
            $data['to_date'] = $dates['end_date'];
        }

        $data['upload_url'] = Url::areaCoverageUpload;

        $view = 'areacoverage_block';

        if ($this->user->block_id) {
            $filter = [
                'block_id' => $this->user->block_id,
                'year_id' => getCurrentYearId(),
                'season' => getCurrentSeason(),
                'start_date' => $this->request->getGet('start_date'),
            ];

            $blocks = $this->areacoveragemodel->getAreaCoverage($filter);
            // printr($blocks);
            // exit;
            $data['start_date'] = '';
            $weekDate = $this->areacoveragemodel->getWeekDate();
            if ($weekDate) {
                $data['start_date'] = $this->areacoveragemodel->getWeekDate()['start_date'];
            }
            $data['filtered_data_url'] = admin_url('areacoverage/filtered');
            $weeks = $this->areacoveragemodel->getWeeks();
            $data['weeks'] = [];
            $week_start_date = '';
            foreach ($weeks as $week) {
                //dropdown weeks
                if (strtotime($week['start_date']) <= strtotime('today')) {
                    $data['weeks'][$week['start_date']] = $week_start_date = $week['start_date'];
                }
                //show week text
//                if (strtotime($week['start_date']) <= strtotime($data['start_date'])) {
//                    $week_text = date('d F', strtotime($week['start_date'])) . '-' . date('d F', strtotime($week['end_date']));
//                }
            }
            $data['week_start_date'] = $data['start_date'];
            $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
                $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
                $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
                $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
                $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = $total_crop_diversification_farmers = $total_crop_diversification_area = $total_rice_fallow_farmers = $total_rice_fallow_area = 0;

            $data['blocks'] = [];
            //            dd($blocks);
            foreach ($blocks as $block) {
                $status = $block->status;
                if (!isset($status)) {
                    $status = 3;
                }
                $action = '';
                $week = '';
                if ($block->start_date) {
                    $href = admin_url('areacoverage/edit?id=' . $block->cc_id);
                    $action .= '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
                                            <i class="fa fa-list"></i></a>';
                    $week = date('d F', strtotime($block->start_date));
                }

                $total_area = $block->fc_area +
                    $block->ragi_smi +
                    $block->ragi_lt +
                    $block->ragi_ls +
                    $block->little_millet_lt +
                    $block->little_millet_ls +
                    $block->foxtail_ls +
                    $block->sorghum_ls +
                    $block->kodo_ls +
                    $block->barnyard_ls +
                    $block->pearl_ls;
                $total_ragi = $block->ragi_smi +
                    $block->ragi_lt +
                    $block->ragi_ls;
                //echo 'total_area:'.$total_area.' total_ragi:'.$total_ragi.' fc_area:'.$block->fc_area.'<br>';

                //                $total_non_ragi = $total_area - $total_ragi - $block->fc_area;
                //subtraction issue for float values
                $total_non_ragi = bcsub(bcsub($total_area, $total_ragi, 2), $block->fc_area, 2);

                $data['blocks'][] = [
                    'week' => $week,
                    'gp' => $block->gp,
                    'farmers_covered' => $block->farmers_covered,
                    'nursery_raised' => $block->nursery_raised,
                    'balance_smi' => $block->balance_smi,
                    'balance_lt' => $block->balance_lt,
                    'ragi_smi' => $block->ragi_smi,
                    'ragi_lt' => $block->ragi_lt,
                    'ragi_ls' => $block->ragi_ls,
                    'little_millet_lt' => $block->little_millet_lt,
                    'little_millet_ls' => $block->little_millet_ls,
                    'foxtail_ls' => $block->foxtail_ls,
                    'sorghum_ls' => $block->sorghum_ls,
                    'kodo_ls' => $block->kodo_ls,
                    'barnyard_ls' => $block->barnyard_ls,
                    'pearl_ls' => $block->pearl_ls,
                    'total_ragi' => $total_ragi,
                    'total_non_ragi' => $total_non_ragi,
                    'total_fc' => $block->fc_area,
                    'total_area' => $total_area,
                    'crop_diversification_farmers' => $block->crop_diversification_farmers,
                    'crop_diversification_area' => $block->crop_diversification_area,
                    'rice_fallow_farmers' => $block->rice_fallow_farmers,
                    'rice_fallow_area' => $block->rice_fallow_area,
                    // 'status' => $this->statuses[$status],
                    'status' => '<label class="badge badge-' . $this->colors_ac[$status] . '">' . $this->statuses[$status] . '</label>',
                    'action' => $action,
                ];

                //calc total
                $total_farmers_covered += $block->farmers_covered;
                $total_nursery_raised += $block->nursery_raised;
                $total_balance_smi += $block->balance_smi;
                $total_balance_lt += $block->balance_lt;
                $total_ragi_smi += $block->ragi_smi;
                $total_ragi_lt += $block->ragi_lt;
                $total_ragi_ls += $block->ragi_ls;
                $total_little_millet_lt += $block->little_millet_lt;
                $total_little_millet_ls += $block->little_millet_ls;
                $total_foxtail_ls += $block->foxtail_ls;
                $total_sorghum_ls += $block->sorghum_ls;
                $total_kodo_ls += $block->kodo_ls;
                $total_barnyard_ls += $block->barnyard_ls;
                $total_pearl_ls += $block->pearl_ls;
                $total_total_ragi += $total_ragi;
                $total_total_non_ragi += $total_non_ragi;
                $total_fc_area += $block->fc_area;
                $total_crop_diversification_farmers += $block->crop_diversification_farmers;
                $total_crop_diversification_area += $block->crop_diversification_area;
                $total_rice_fallow_farmers += $block->rice_fallow_farmers;
                $total_rice_fallow_area += $block->rice_fallow_area;
                $total_total_area += $total_area;

            }

            $data['blocks'][] = [
                'week' => '',
                'gp' => '<strong>Total</strong>',
                'farmers_covered' => $total_farmers_covered,
                'nursery_raised' => $total_nursery_raised,
                'balance_smi' => $total_balance_smi,
                'balance_lt' => $total_balance_lt,
                'ragi_smi' => $total_ragi_smi,
                'ragi_lt' => $total_ragi_lt,
                'ragi_ls' => $total_ragi_ls,
                'little_millet_lt' => $total_little_millet_lt,
                'little_millet_ls' => $total_little_millet_ls,
                'foxtail_ls' => $total_foxtail_ls,
                'sorghum_ls' => $total_sorghum_ls,
                'kodo_ls' => $total_kodo_ls,
                'barnyard_ls' => $total_barnyard_ls,
                'pearl_ls' => $total_pearl_ls,
                'total_ragi' => $total_total_ragi,
                'total_non_ragi' => $total_total_non_ragi,
                'total_fc' => $total_fc_area,
                'total_area' => $total_total_area,
                'crop_diversification_farmers' => $total_crop_diversification_farmers,
                'crop_diversification_area' => $total_crop_diversification_area,
                'rice_fallow_farmers' => $total_rice_fallow_farmers,
                'rice_fallow_area' => $total_rice_fallow_area,
                'status' => '',
                'action' => ''
            ];

            $view = 'areacoverage_block';
        } else if ($this->user->district_id) {
            //            $filter = [
//                'district_id' => $this->user->district_id,
//                'year_id' => getCurrentYearId(),
//                'season' => getCurrentSeason()
//            ];

            //            $districts = $this->areacoveragemodel->getAreaCoverage($filter);
            $view = 'areacoverage_district';
        }

        return $this->template->view('Admin\CropCoverage\Views\\' . $view, $data);
    }

    public function download()
    {

        $dates = $this->areacoveragemodel->getWeekDate();

        if (!$dates) {
            return redirect()->to(admin_url('areacoverage'))
                ->with('message', 'Dates are not selected');
        }

        $data['from_date'] = $dates['start_date'];
        $data['to_date'] = $dates['end_date'];

        $reader = IOFactory::createReader('Xlsx');
        $current_season = $this->areacoveragemodel->getCurrentYearDates()['season'];

        if ($current_season === 'Kharif') {
            $template_file = DIR_TEMPLATE . 'area_coverage_kharif.xlsx';
        } elseif ($current_season === 'Rabi') {
            $template_file = DIR_TEMPLATE . 'area_coverage_rabi.xlsx';
        }


        $spreadsheet = $reader->load($template_file);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('W1', $data['from_date']);
        $sheet->setCellValue('Z1', $data['to_date']);
        $year_text = getCurrentYear();
        $sheet->setCellValue('F1', 'District wise weekly Crop Progress under OMM during ' . $year_text);



        $fin_year = getCurrentYear();

        $gps = (new GrampanchayatModel())->getGPsByBlock($this->user->block_id);

        if (!$gps) {
            return redirect()->to(admin_url('areacoverage'))
                ->with('message', 'No GPs found. Please add GPs first.');
        }

        $row = 4;
        if ($current_season === 'Kharif') {
            foreach ($gps as $key => $gp) {
                $row++;
                $sheet->setCellValue("A$row", $gp->block_id);
                $sheet->setCellValue("B$row", $gp->gp_id);
                $sheet->setCellValue("C$row", ($key + 1));
                $sheet->setCellValue("D$row", $gp->block);
                $sheet->setCellValue("E$row", $gp->gp);

                $sheet->setCellValue("T$row", "=J$row+K$row+L$row");
                $sheet->setCellValue("U$row", "=SUM(M$row:S$row)");
                $sheet->setCellValue("AE$row", "=SUM(V$row:AB$row)");
                $sheet->setCellValue("AF$row", "=SUM(T$row:AB$row)");

            }
        } else {
            foreach ($gps as $key => $gp) {
                $row++;
                $sheet->setCellValue("A$row", $gp->block_id);
                $sheet->setCellValue("B$row", $gp->gp_id);
                $sheet->setCellValue("C$row", ($key + 1));
                $sheet->setCellValue("D$row", $gp->block);
                $sheet->setCellValue("E$row", $gp->gp);

                $sheet->setCellValue("T$row", "=J$row+K$row+L$row");
                $sheet->setCellValue("U$row", "=SUM(M$row:S$row)");
                $sheet->setCellValue("AG$row", "=SUM(V$row:AB$row)");
                $sheet->setCellValue("AH$row", "=SUM(T$row:AB$row,AD$row)");

            }
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
        $validation->setType(DataValidation::TYPE_DECIMAL);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Only numbers/decimals allowed!');
        $validation->setPromptTitle('Allowed input');
        $validation->setPrompt('Only numbers greater than or equal to 0 are allowed.');
        $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
        $validation->setFormula1("=0");
        $validation->setSqref("F5:AF$row");

        if ($current_season === 'Kharif') {
            $cells = "F5:S$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            $cells = "V5:AD$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        } else {
            $cells = "F5:S$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            $cells = "V5:AF$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        }

        //        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="area_coverage_' . $this->user->username . '_' . $current_season . '_' . $fin_year . '_' . date('Y_m_d_His') . '.xlsx');

        $writer->save("php://output");
        exit;
    }

    public function upload()
    {
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
                'status' => false,
                'message' => 'Invalid file',
                'errors' => $this->validator->getErrors()
            ]);
        } else {
            $acModel = new AreaCoverageModel();
            $file = $this->request->getFile('file');

            try {
                $reader = IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($file);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid file.'
                ]);
            }

            $activesheet = $spreadsheet->getSheet(0);

            $row_data = $activesheet->toArray();
            // printr($row_data);
            // exit;

            $dates = $this->areacoveragemodel->getWeekDate();

            $current = $this->areacoveragemodel->getCurrentYearDates();

            $from_date = $dates['start_date'];
            $to_date = $dates['end_date'];
            $excel_from_date = isset($row_data[0][22]) ? $row_data[0][22] : 0;

            $exists = $acModel
                ->where('start_date', $from_date)
                ->where('block_id', $this->user->block_id)
                ->where('season', $current['season'])
                ->where('year_id', getCurrentYearId())
                ->first();
            //            $exists = false;

            //gp belongs to the block
            $gp_cell = isset($row_data[4][1]) ? $row_data[4][1] : null;

            $gp = [];
            $gp_belongs = false;

            if ($gp_cell) {
                $gp = (new GrampanchayatModel())->find($gp_cell);
            }
            if ($gp) {
                $gp_belongs = $gp->block_id == $this->user->block_id;
            }

            //validation
            if (!isset($row_data[0][22])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid file. Please download the file from here and upload again.'
                ]);
            } else if (strtotime($from_date) != strtotime($excel_from_date)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid week dates. Please download the latest file and upload again.'
                ]);
            } else if ($exists) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'This week data is already uploaded.'
                ]);
            } else if (!$gp_belongs) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'The GP dont belong to your block. Please download the file and try again.'
                ]);
            } else {
                $crops = (new CropsModel())->findAll();

                foreach ($row_data as $gp) {
                    //only rows with gp_id
                    if (is_numeric($gp[0])) {
                        $master = [
                            'start_date' => $from_date,
                            'end_date' => $to_date,
                            'district_id' => $this->user->district_id,
                            'year_id' => getCurrentYearId(),
                            'season' => $current['season'],
                            'block_id' => $gp[0],
                            'gp_id' => $gp[1],
                            'farmers_covered' => $gp[5],
                            'crop_diversification_farmers' => $gp[30],
                            'crop_diversification_area' => $gp[31],
                            'rice_fallow_farmers' => $gp[28],
                            'rice_fallow_area' => $gp[29],
                        ];

                        //                        $ac_crop_coverage_id = 0;
                        $ac_crop_coverage_id = $acModel->insert($master);

                        $col = 5;
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

                        $col += 2;
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
            'status' => true,
            'message' => 'Upload successful.',
            'url' => admin_url('areacoverage')
        ]);
    }

    public function edit()
    {
        $cc_id = $this->request->getGet('id');

        $dates = $this->areacoveragemodel->getWeekDate();

        $to_date = $dates['end_date'];

        $data['show_form'] = false;
        if (strtotime($to_date) >= strtotime('today')) {
            $data['show_form'] = true;
        }

        if ($this->request->getMethod(1) == 'POST') {
            $master = [
                'farmers_covered' => $this->request->getPost('crop_coverage')['farmers_covered'],
                'crop_diversification_farmers' => $this->request->getPost('crop_coverage')['crop_diversification_farmers'],
                'crop_diversification_area' => $this->request->getPost('crop_coverage')['crop_diversification_area'],
                'rice_fallow_farmers' => $this->request->getPost('crop_coverage')['rice_fallow_farmers'],
                'rice_fallow_area' => $this->request->getPost('crop_coverage')['rice_fallow_area'],
                'status' => 0,
                'remarks' => ''
            ];
            $this->areacoveragemodel->update($cc_id, $master);

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

            return redirect()->to(admin_url('areacoverage'))->with('message', 'Area coverage data updated.');
        }

        return $this->getForm();
    }

    protected function getForm($return_data = false)
    {
        $cc_id = $this->request->getGet('id');


        $cc_info = $this->areacoveragemodel->find($cc_id);
        // printr($cc_info);
        // exit;
        if (!$cc_info) {
            return redirect()->to(admin_url('areacoverage'))->with('message', 'Could not find the data requested');
        }

        $data['show_form'] = false;
        //there is submit button open after approved
        //code by HKS
        if (($cc_info->status != 1) && ($cc_info->block_id == $this->user->block_id)) {
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

        $data['crop_coverage'] = [
            'farmers_covered' => $cc_info->farmers_covered,
            'crop_diversification_farmers' => $cc_info->crop_diversification_farmers,
            'crop_diversification_area' => $cc_info->crop_diversification_area,
            'rice_fallow_farmers' => $cc_info->rice_fallow_farmers,
            'rice_fallow_area' => $cc_info->rice_fallow_area
        ];

        $data['nursery_info'] = $this->areacoveragemodel->getNursery($cc_id);

        $data['crops'] = [];
        $smi = $lt = $ls = 0;
        foreach ($cropPrtcArea as $area) {
            $practices = [];
            foreach ($cropPrtcArea as $p) {
                if ($area['crop_id'] == $p['crop_id']) {
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
        $_practices = ['smi', 'lt', 'ls'];
        foreach ($cropPrtcArea as $area) {
            foreach ($_practices as $_practice) {
                if (strtolower($area['practice']) == $_practice) {
                    $$_practice += $area['area'];
                }
            }
        }

        //cacl total
        foreach ($_practices as $practice) {
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
        // printr($data['fups']);
        // exit;
        $area = 0;
        foreach ($data['fups'] as $fup) {
            $area += $fup['area'];
        }

        $data['fups_total'] = $area;
        $data['practices'] = $_practices;

        if ($return_data) {
            return $data;
        }

        return $this->template->view('Admin\CropCoverage\Views\areacoverage_edit', $data);
    }

    public function delete()
    {
        $this->template->set_meta_title('Delete area coverage');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'Delete area coverage',
            'href' => admin_url('areacoverage/delete')
        );

        $this->template->add_package(array('datatable', 'select2'), true);

        $data['heading_title'] = 'Area Coverage Delete';

        $data['text_list'] = lang('Approve.text_list');
        $data['text_no_results'] = lang('Approve.text_no_results');
        $data['text_confirm'] = lang('Approve.text_confirm');

        $data['button_add'] = lang('Add Target');
        $data['button_edit'] = lang('Edit Target');
        $data['button_delete'] = lang('Approve.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $district_id = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = $district_id = 0;
        }

        $week_dates = $this->areacoveragemodel->getWeekDate();

        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $start_date = $this->request->getGet('start_date');
        } else if ($week_dates) {
            $data['start_date'] = $start_date = $week_dates['start_date'];
        } else {
            $data['start_date'] = $start_date = '';
        }

        $acModel = new AreaCoverageModel();

        $filter = [
            'district_id' => $district_id,
            'year_id' => getCurrentYearId(),
            'season' => getCurrentSeason(),
            'start_date' => $data['start_date']
        ];

        //update status
        if ($this->request->getMethod(1) == 'POST') {
            //delete
            $filter['block_id'] = $this->request->getPost('blocks');
            $this->areacoveragemodel->deleteAll($filter);

            return redirect()->to(admin_url('areacoverage/delete?district_id=' . $district_id . '&start_date=' . $start_date))
                ->with('message', 'The records have been deleted.');
        }

        $blocks = [];
        if ($district_id) {
            $blocks = $this->areacoveragemodel->getAreaCoverage($filter);
        }

        $data['blocks'] = [];
        $week_text = '';
        if ($blocks) {
            foreach ($blocks as $block) {
                $action = '';
                if ($block->start_date) {
                    $week_text = date('d F', strtotime($block->start_date)) . '-' . date('d F', strtotime($block->end_date));
                }
                $status = $block->status;
                if (!isset($status)) {
                    $status = 3;
                }
                $total_area = $block->fc_area +
                    $block->ragi_smi +
                    $block->ragi_lt +
                    $block->ragi_ls +
                    $block->little_millet_lt +
                    $block->little_millet_ls +
                    $block->foxtail_ls +
                    $block->sorghum_ls +
                    $block->kodo_ls +
                    $block->barnyard_ls +
                    $block->pearl_ls;
                $total_ragi = $block->ragi_smi +
                    $block->ragi_lt +
                    $block->ragi_ls;
                $total_non_ragi = $total_area - $total_ragi - $block->fc_area;
                $data['blocks'][] = [
                    'block_id' => $block->block_id,
                    'block' => $block->block,
                    'gps' => $block->total_gps,
                    'farmers_covered' => $block->farmers_covered,
                    'nursery_raised' => $block->nursery_raised,
                    'balance_smi' => $block->balance_smi,
                    'balance_lt' => $block->balance_lt,
                    'total_ragi' => $total_ragi,
                    'total_non_ragi' => $total_non_ragi,
                    'total_fc' => $block->fc_area,
                    'total_area' => $total_area,
                    'status' => $this->statuses[$status],
                    'action' => $action,
                ];

            }
        }

        $weeks = $this->areacoveragemodel->getWeeks();

        $data['weeks'] = [];
        $week_start_date = '';
        foreach ($weeks as $week) {
            //dropdown weeks
            if (strtotime($week['start_date']) <= strtotime('today')) {
                $data['weeks'][$week['start_date']] = $week_start_date = $week['start_date'];
            }
            //show week text
            if (strtotime($week['start_date']) <= strtotime($data['start_date'])) {
                $week_text = date('d F', strtotime($week['start_date'])) . '-' . date('d F', strtotime($week['end_date']));
            }
        }

        $data['week_start_date'] = $data['start_date'];

        $data['week_text'] = $week_text;

        $data['districts'] = [];
        $districts = (new DistrictModel())->orderBy('name')->findAll();

        $data['districts'][0] = 'Select District';
        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $district_status = $acModel->where('district_id', $district_id)
            ->where('start_date', $start_date)->first();

        $data['status'] = '';
        $data['remarks'] = '';
        $data['status_color'] = '';
        if ($district_status) {
            $data['status'] = $this->statuses[$district_status->status];
            $data['status_color'] = $this->colors[$district_status->status];
            $data['remarks'] = $district_status->remarks;
        }

        return $this->template->view('Admin\CropCoverage\Views\delete_district', $data);
    }

}