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

class AreaCoverage extends AdminController
{
    private $cropsModel;
    private $districtModel;
    private $acModel;
    private $blockModel;
    private $grampanchayatModel;
    private $yearModel;


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
        $this->acModel = new AreaCoverageModel();
        $this->blockModel = new BlockModel();
        $this->grampanchayatModel = new GrampanchayatModel();
        $this->yearModel = new YearModel();


    }
    public function index()
    {

        $this->template->set_meta_title(lang('Seasons Data.heading_title'));

        return $this->getList();
    }

    protected function getList()
    {
        $data['season'] = getCurrentSeason();
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);

        $data['filtered_data_url'] = admin_url('areacoverage/filtered');
        $data['download_url'] = admin_url('areacoverage/download');
        $data['heading_title'] = lang('Add Area Coverage');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        $data['currentDay'] = date('l');

        $data['isActiveDay'] = in_array($data['currentDay'], array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'));
        $data['inActiveDay'] = !$data['isActiveDay'];
        $dates = $this->acModel->getWeekDate();
        // printr($dates);
        // exit;
        $data['from_date'] = '';
        $data['to_date'] = '';
        if ($dates) {
            $data['from_date'] = $dates['start_date'];
            $data['to_date'] = $dates['end_date'];
        }

        $data['upload_url'] = Url::areaCoverageUpload;
        if ($this->request->getGet('start_date')) {
            $data['get_date'] = $this->request->getGet('start_date');
        } else {
            $data['get_date'] = $dates['start_date'];
        }
        // if ($data['get_date']) {
        //     $data['start_date'] = $dates['get_date'];
        // } else {
        //     $data['start_date'] = $dates['start_date'];
        // }
        // var_dump($this->request->getGet('start_date'));
        // var_dump($dates['start_date']);

        if ($this->user->block_id) {
            $filter = [
                'block_id' => $this->user->block_id,
                'year_id' => getCurrentYearId(),
                'season' => getCurrentSeason(),
                'start_date' => $data['get_date']
            ];
            $data['start_date'] = $data['get_date'];
            // printr($filter);
            // exit;

            //$blocks = $this->acModel->getAreaCoverage($filter);
            $gps = $this->acModel->getAreaCoverageBlock($filter);
            // printr($gps);
            // exit;
            $blocks = [];
            foreach ($gps as $gp) {
                $gp->total_ragi = 0;
                $gp->total_non_ragi = 0;
                $achievements = $this->acModel->getAchivementByCCID((int) $gp->cc_id);

                $action = '';
                $week = '';
                $action = '';
                $week = '';
                if ($gp->start_date) {
                    $href = admin_url('areacoverage/edit?id=' . $gp->cc_id);
                    $action .= '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
                                            <i class="fa fa-list"></i></a>';

                    $week = date('d F', strtotime($gp->start_date)) . '-' . date('d F', strtotime($gp->end_date));
                }

                $status = $gp->status;
                if (!isset($status)) {
                    $status = 3;
                }
                foreach ($achievements as $achievement) {
                    if ($achievement->crop_type) {
                        $gp->total_ragi += $achievement->ls + $achievement->smi + $achievement->lt;
                    } else {
                        $gp->total_non_ragi += $achievement->ls + $achievement->smi + $achievement->lt;
                    }
                }
                $rice_fallow = $this->acModel->getRiceFallowByCCID((int) $gp->cc_id);
                $follow_up = $this->acModel->getFollowUpByCCID((int) $gp->cc_id);

                $gp->rice_fallow = $rice_fallow;
                $gp->follow_up = $follow_up;
                $gp->total_area = $gp->total_ragi + $gp->total_non_ragi + $gp->follow_up;
                $gp->achievements = $achievements;
                $gp->action = $action;
                $gp->status = '<label class="badge badge-' . $this->colors_ac[$status] . '">' . $this->statuses[$status] . '</label>';
                $gp->week = $week;
                $blocks[] = $gp;
            }
            $data['blocks'] = $blocks;
            // printr($data['blocks']);
            // exit;
            $weekDate = $this->acModel->getWeekDate();
            $data['start_date'] = '';
            if ($weekDate) {
                $data['start_date'] = $this->acModel->getWeekDate()['start_date'];
            }
            $data['filtered_data_url'] = admin_url('areacoverage/filtered');
            $weeks = $this->acModel->getWeeks();

            $data['weeks'][0] = 'All Weeks';
            $week_text = '';

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
            // printr($data['weeks']);
            // exit;
            $data['week_start_date'] = $data['start_date'];

            $data['week_text'] = $week_text;
            $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
                $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
                $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
                $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
                $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_rfc_area = $total_total_area = $total_crop_div_area = 0;

            //$data['blocks'] = [];

            // foreach ($blocks as $block) {
            //     $status = $block->status;
            //     if (!isset($status)) {
            //         $status = 3;
            //     }
            //     $action = '';
            //     $week = '';
            //     if ($block->start_date) {
            //         $href = admin_url('areacoverage/edit?id=' . $block->cc_id);
            //         $action .= '<a href="' . $href . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-title="View">
            //                                 <i class="fa fa-list"></i></a>';
            //         $week = date('d F', strtotime($block->start_date));
            //     }

            //     $total_area = $block->fc_area +
            //         $block->ragi_smi +
            //         $block->ragi_lt +
            //         $block->ragi_ls +
            //         $block->little_millet_lt +
            //         $block->little_millet_ls +
            //         $block->foxtail_ls +
            //         $block->sorghum_ls +
            //         $block->kodo_ls +
            //         $block->barnyard_ls +
            //         $block->pearl_ls;
            //     $total_ragi = $block->ragi_smi +
            //         $block->ragi_lt +
            //         $block->ragi_ls;
            //     $total_crop_div_area = $block->crop_div_ragi +
            //         $block->crop_div_non_ragi;

            //     $total_non_ragi = bcsub(bcsub($total_area, $total_ragi, 2), $block->fc_area, 2);

            //     $data['blocks'][] = [
            //         'week' => $week,
            //         'gp' => $block->gp,
            //         'farmers_covered' => $block->farmers_covered,
            //         'nursery_raised' => $block->nursery_raised,
            //         'balance_smi' => $block->balance_smi,
            //         'balance_lt' => $block->balance_lt,
            //         'ragi_smi' => $block->ragi_smi,
            //         'ragi_lt' => $block->ragi_lt,
            //         'ragi_ls' => $block->ragi_ls,
            //         'little_millet_lt' => $block->little_millet_lt,
            //         'little_millet_ls' => $block->little_millet_ls,
            //         'foxtail_ls' => $block->foxtail_ls,
            //         'sorghum_ls' => $block->sorghum_ls,
            //         'kodo_ls' => $block->kodo_ls,
            //         'barnyard_ls' => $block->barnyard_ls,
            //         'pearl_ls' => $block->pearl_ls,
            //         'total_ragi' => $total_ragi,
            //         'total_non_ragi' => $total_non_ragi,
            //         'total_fc' => $block->fc_area,
            //         'total_area' => $total_area,
            //         'total_crop_div_area' => $total_crop_div_area,
            //         'total_rfc' => $block->rfc_area,
            //         'status' => '<label class="badge badge-' . $this->colors_ac[$status] . '">' . $this->statuses[$status] . '</label>',
            //         'action' => $action,
            //     ];

            //     $total_farmers_covered = $block->farmers_covered;
            //     $total_nursery_raised = $block->nursery_raised;
            //     $total_balance_smi = $block->balance_smi;
            //     $total_balance_lt = $block->balance_lt;
            //     $total_ragi_smi += $block->ragi_smi;
            //     $total_ragi_lt += $block->ragi_lt;
            //     $total_ragi_ls += $block->ragi_ls;
            //     $total_little_millet_lt += $block->little_millet_lt;
            //     $total_little_millet_ls += $block->little_millet_ls;
            //     $total_foxtail_ls += $block->foxtail_ls;
            //     $total_sorghum_ls += $block->sorghum_ls;
            //     $total_kodo_ls += $block->kodo_ls;
            //     $total_barnyard_ls += $block->barnyard_ls;
            //     $total_pearl_ls += $block->pearl_ls;
            //     $total_total_ragi += $total_ragi;
            //     $total_total_non_ragi += $total_non_ragi;
            //     $total_fc_area += $block->fc_area;
            //     $total_rfc_area += $block->rfc_area;
            //     $total_crop_div_area += $total_crop_div_area;
            //     $total_total_area += $total_area;
            // }
        }
        // printr($blocks);
        // exit;
        if ($this->user->block_id) {
            return $this->template->view('Admin\CropCoverage\Views\areacoverage_block', $data);
        } else {
            return redirect()->to(admin_url())
                ->with('message', 'Please Login As a Block Admin');
        }
    }

    public function download()
    {

        $dates = $this->acModel->getWeekDate();

        if (!$dates) {
            return redirect()->to(admin_url('areacoverage'))
                ->with('message', 'Dates are not selected');
        }

        $data['from_date'] = $dates['start_date'];
        $data['to_date'] = $dates['end_date'];

        $reader = IOFactory::createReader('Xlsx');
        $current_season = $this->acModel->getCurrentYearDates()['season'];

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

        $gps = $this->grampanchayatModel->getGPsByBlock($this->user->block_id);

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
                $sheet->setCellValue("AC$row", "=SUM(V$row:AB$row)");
                $sheet->setCellValue("AD$row", "=SUM(T$row:AB$row)");
                $sheet->setCellValue("AG$row", "= AE$row+AF$row");
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
                $sheet->setCellValue("AC$row", "=SUM(V$row:AB$row)");
                $sheet->setCellValue("AD$row", "=SUM(T$row:AB$row)");
                $sheet->setCellValue("AL$row", "=SUM(AE$row:AK$row)");
                $sheet->setCellValue("AO$row", "=SUM(AM$row+AN$row)");
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
        $validation->setSqref("F5:AO$row");

        if ($current_season === 'Kharif') {
            $cells = "F5:S$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            $cells = "V5:AD$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            $cells = "AE5:AF$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);


        } else {
            $cells = "F5:S$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            $cells = "V5:AB$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            $cells = "AE5:AK$row";
            $sheet->getStyle($cells)
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            $cells = "AM5:AN$row";
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
            $dates = $this->acModel->getWeekDate();

            $current = $this->acModel->getCurrentYearDates();

            $from_date = $dates['start_date'];
            $to_date = $dates['end_date'];
            $excel_from_date = isset($row_data[0][22]) ? $row_data[0][22] : 0;

            $exists = $this->acModel
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
                $gp = $this->grampanchayatModel->find($gp_cell);
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
                $crops = $this->cropsModel->findAll();

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
                            'crop_div_ragi' => $gp[38],
                            'crop_div_non_ragi' => $gp[39],

                        ];

                        $ac_crop_coverage_id = 0;
                        $ac_crop_coverage_id = $this->acModel->insert($master);

                        $col = 5;
                        $nursery = [
                            'crop_coverage_id' => $ac_crop_coverage_id,
                            'nursery_raised' => $gp[++$col],
                            'balance_smi' => $gp[++$col],
                            'balance_lt' => $gp[++$col],
                        ];

                        $this->acModel->addNursery($nursery);

                        $cropPractices = $this->acModel->getCropPractices();

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

                        $this->acModel->addArea($areas);

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
                        $this->acModel->addFupCrops($fCrop);
                        //Rice fallow crops
                        if ($current['season'] == "Rabi") {
                            $col += 2;
                            $rfcrop = [];
                            foreach ($crops as $crop) {

                                $rfcrop[] = [
                                    'crop_coverage_id' => $ac_crop_coverage_id,
                                    'crop_id' => $crop->id,
                                    'area' => $gp[++$col],

                                ];
                            }

                            $this->acModel->addRiceFallowCrops($rfcrop);
                        }
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

        $dates = $this->acModel->getWeekDate();

        $to_date = $dates['end_date'];

        $data['show_form'] = false;
        if (strtotime($to_date) >= strtotime('today')) {
            $data['show_form'] = true;
        }

        if ($this->request->getMethod(1) == 'POST') {
            $master = [
                'farmers_covered' => $this->request->getPost('crop_coverage')['farmers_covered'],
                'crop_div_ragi' => $this->request->getPost('crop_coverage')['crop_div_ragi'],
                'crop_div_non_ragi' => $this->request->getPost('crop_coverage')['crop_div_non_ragi'],
                'status' => 0,
                'remarks' => ''
            ];
            $this->acModel->update($cc_id, $master);

            $nursery = [
                'crop_coverage_id' => $cc_id,
                'nursery_raised' => $this->request->getPost('nursery')['nursery_raised'],
                'balance_smi' => $this->request->getPost('nursery')['balance_smi'],
                'balance_lt' => $this->request->getPost('nursery')['balance_lt'],
            ];

            $this->acModel->deleteNursery($cc_id);
            $this->acModel->addNursery($nursery);

            $cropPractices = $this->acModel->getCropPractices();

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

            $this->acModel->deleteArea($cc_id);
            $this->acModel->addArea($areas);

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

            $this->acModel->deleteFupCrops($cc_id);
            $this->acModel->addFupCrops($fCrop);

            $rfCrop = [];
            foreach ($crops as $crop) {
                $rfCrop[] = [
                    'crop_coverage_id' => $cc_id,
                    'crop_id' => $crop->id,
                    'area' => $this->request->getPost('ricefallow')[$crop->id],
                ];
            }

            $this->acModel->deleteRiceFallowCrops($cc_id);
            $this->acModel->addRiceFallowCrops($rfCrop);

            return redirect()->to(admin_url('areacoverage'))->with('message', 'Area coverage data updated.');
        }

        return $this->getForm();
    }

    protected function getForm($return_data = false)
    {
        $cc_id = $this->request->getGet('id');


        $cc_info = $this->acModel->find($cc_id);

        if (!$cc_info) {
            return redirect()->to(admin_url('areacoverage'))->with('message', 'Could not find the data requested');
        }

        $data['show_form'] = false;
        //there is submit button open after approved
        //code by HKS
        if (($cc_info->status != 1) && ($cc_info->block_id == $this->user->block_id)) {
            $data['show_form'] = true;
        }

        $data['district'] = $this->districtModel->find($cc_info->district_id)->name;
        $data['block'] = $this->blockModel->find($cc_info->block_id)->name;
        $data['gp'] = $this->grampanchayatModel->find($cc_info->gp_id)->name;
        $data['year'] = $this->yearModel->find($cc_info->year_id)->name;
        $data['season'] = $cc_info->season;
        $data['date_added'] = ymdToDmy($cc_info->created_at);
        $data['start_date'] = ymdToDmy($cc_info->start_date);
        $data['end_date'] = ymdToDmy($cc_info->end_date);

        $cropPrtcArea = $this->acModel->getPracticeArea($cc_id);

        $data['crop_coverage'] = [
            'farmers_covered' => $cc_info->farmers_covered,
            'crop_div_ragi' => $cc_info->crop_div_ragi,
            'crop_div_non_ragi' => $cc_info->crop_div_non_ragi,
        ];
        $data['total_div_crop'] = $cc_info->crop_div_ragi + $cc_info->crop_div_non_ragi;

        $data['nursery_info'] = $this->acModel->getNursery($cc_id);

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
        $data['fups'] = $this->acModel->getFupCrops($cc_id);
        $data['ricefallows'] = $this->acModel->getRiceFallowCrops($cc_id);
        // printr($data['fups']);
        // exit;
        $area = 0;
        foreach ($data['fups'] as $fup) {
            $area += $fup['area'];
        }
        $data['fups_total'] = $area;
        // printr($data['fups_total']);
        // exit;
        $data['practices'] = $_practices;
        $rfarea = 0;
        foreach ($data['ricefallows'] as $ricefallow) {
            $rfarea += $ricefallow['area'];
        }
        $data['rfc_total'] = $rfarea;

        // printr($data['rfc_total']);
        // exit;
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

        $data['button_delete'] = lang('Approve.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $district_id = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = $district_id = 0;
        }

        $week_dates = $this->acModel->getWeekDate();

        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $start_date = $this->request->getGet('start_date');
        } else if ($week_dates) {
            $data['start_date'] = $start_date = $week_dates['start_date'];
        } else {
            $data['start_date'] = $start_date = '';
        }



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
            $this->acModel->deleteAll($filter);

            return redirect()->to(admin_url('areacoverage/delete?district_id=' . $district_id . '&start_date=' . $start_date))
                ->with('message', 'The records have been deleted.');
        }

        $blocks = [];
        if ($district_id) {
            $blocks = $this->acModel->getAreaCoverage($filter);
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

        $weeks = $this->acModel->getWeeks();

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
        $districts = $this->districtModel->orderBy('name')->findAll();

        $data['districts'][0] = 'Select District';
        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $district_status = $this->acModel->where('district_id', $district_id)
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