<?php
namespace Admin\Reports\Controllers;

use Admin\Common\Models\YearModel;
use Admin\CropCoverage\Models\AreaCoverageModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;
use App\Libraries\Export;
use Config\Url;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AreaCoverage extends AdminController
{
    private $districtModel;
    private $acModel;
    private $cropsModel;
    private $blockModel;
    private $yearModel;
    public $statuses = ['Uploaded', 'Approved', 'Rejected', 'Not Uploaded', 'Not Required'];
    public $colors_ac = [
        'warning',
        'success',
        'danger',
        'secondary',
        'primary',
    ];
    public function __construct()
    {
        $this->districtModel = new DistrictModel();
        $this->acModel = new AreaCoverageModel();
        $this->cropsModel = new CropsModel();
        $this->blockModel = new BlockModel();
        $this->yearModel = new YearModel();
    }

    public function index($action = '')
    {
        $data = [];
        $data['title'] = 'Area Coverage Report';
        $data['years'] = getAllYears();
        $data['seasons'] = $this->acModel->getSeasons();
        $dates = $this->acModel->getWeekDate();
        $data['districts'] = $this->districtModel->getAll();

        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        } else {
            $data['year_id'] = getCurrentYearId();
        }
        if ($this->request->getGet('season')) {
            $data['current_season'] = $season = $this->request->getGet('season');
        } else {
            $data['current_season'] = strtolower(getCurrentSeason());
        }
        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = $this->user->district_id;
        }
        $data['blocks'] = [];
        if ($data['district_id']) {
            $data['blocks'] = $this->blockModel->getAll(['district_id' => $data['district_id']]);
        }
        $data['block_id'] = '';
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        } else {
            $data['block_id'] = $this->user->block_id;
        }
        $data['start_date'] = '';
        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $this->request->getGet('start_date');
        }

        // $data['district_id'] = $this->user->district_id;

        // if ($data['block_id']) {
        //     $filter['block_id'] = $data['block_id'];
        // } else if ($data['district_id']) {
        //     $filter['district_id'] = $data['district_id'];
        // }

        // printr($data['districts']);
        // exit;
        // if ($this->user->block_id) {
        //     $data['block_id'] =  $this->user->block_id;
        //     $data['districts'] = (new DistrictModel())->where('id', $this->user->district_id)->asArray()->find();
        // } else if ($this->user->district_id) {
        //     $data['district_id'] = $filter['district_id'] = $this->user->district_id;
        //     $data['districts'] = (new DistrictModel())->where('id', $this->user->district_id)->asArray()->find();
        // } else {
        //     $data['districts'] = (new DistrictModel())->orderBy('name')->asArray()->find();
        // }
        // echo $data['district_id'];
        // exit;
        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season'],
            'start_date' => $data['start_date'],
            'district_id' => $data['district_id'],
            'block_id' => $data['block_id'],
        ];
        $blocks = $this->acModel->getAreaCoverageReport($filter);
        // printr($blocks);
        // exit;
        if ($data['block_id']) {
            $this->gps($blocks, $data);
        } else if ($data['district_id']) {
            $this->blocks($blocks, $data);
        } else {
            $this->districts($blocks, $data);
        }

        $data['crop_practices'] = $this->acModel->getCropPractices();
        $crops = $this->cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }



        if ($action == 'download') {
            $data['fin_year'] = $this->yearModel->find($data['year_id'])->name;
            $data['table'] = view('Admin\Reports\Views\areacoverage_table', $data);
            $filename = 'AreaCoverageReport_' . $data['current_season'] . '_' . $data['fin_year'] . '_' . date('Y-m-d His') . '.xlsx';

            $spreadsheet = Export::createExcelFromHTML($data['table'], $filename, true);
            if ($spreadsheet) {
                $worksheet = $spreadsheet->getActiveSheet();
                $wordWrapCols = [
                    'A1',
                    'D1',
                    'E1',
                    'F1',
                    'G1',
                    'M2',
                    'O2',
                    'P2',
                    'Q2',
                    'R1',
                    'S1',
                    'T1',
                    'U1'
                ];
                foreach ($wordWrapCols as $col) {
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }

            exit;
        }
        // echo $season;
        // exit;
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
        // printr($weeks);
        // exit;

        $data['week_start_date'] = $data['start_date'];

        $data['week_text'] = $week_text;

        $data['filter_panel'] = view('Admin\Reports\Views\areacoverage_filter', $data);

        $params = 'year_id=' . $data['year_id'];
        $params .= '&season=' . $data['current_season'];
        $params .= '&district_id=' . $data['district_id'];
        $params .= '&block_id=' . $data['block_id'];
        $params .= '&start_date=' . $data['start_date'];

        $data['download_url'] = admin_url('reports/areacoverage/download?' . $params);
        $data['get_blocks'] = Url::getBlocks;
        // echo $data['district_id'];
        // exit;

        return $this->template->view('Admin\Reports\Views\areacoverage', $data);
    }

    private function gps($blocks, &$data)
    {
        // printr($blocks);
        // exit;
        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = $total_rfc_area = $total_crop_div_area = 0;

        $data['rows'] = [];
        foreach ($blocks as $block) {
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
            //            $total_non_ragi = $total_area - $total_ragi - $block->fc_area;

            //subtraction issue for float values
            $total_non_ragi = bcsub(bcsub($total_area, $total_ragi, 2), $block->fc_area, 2);
            $total_crop_div = $block->crop_div_ragi + $block->crop_div_non_ragi;
            $data['rows'][] = [
                'district_name' => $block->district_name,
                'block_name' => $block->block_name,
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
                'total_rfc' => $block->rfc_area,
                'total_crop_div' => $total_crop_div,
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
            $total_total_area += $total_area;
            $total_rfc_area += $block->rfc_area;
            $total_crop_div_area += $total_crop_div;

        }

        $data['rows'][] = [
            'gp' => '<strong>Total</strong>',
            'district_name' => '',
            'block_name' => '',
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
            'total_rfc' => $total_rfc_area,
            'total_crop_div' => $total_crop_div_area,
        ];

    }

    private function blocks($blocks, &$data)
    {
        // printr($blocks);
        // exit;
        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = $total_rfc_area = $total_crop_div_area = 0;

        $data['rows'] = [];
        $gps = 0;
        foreach ($blocks as $block) {
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
            //            $total_non_ragi = $total_area - $total_ragi - $block->fc_area;
            //subtraction issue for float values
            $total_non_ragi = bcsub(bcsub($total_area, $total_ragi, 2), $block->fc_area, 2);
            $total_crop_div = $block->crop_div_ragi + $block->crop_div_non_ragi;
            $data['rows'][] = [
                'block' => $block->block,
                'gps' => $block->total_gps,
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
                'total_rfc' => $block->rfc_area,
                'total_crop_div' => $total_crop_div,
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
            $total_total_area += $total_area;
            $total_rfc_area += $block->rfc_area;
            $total_crop_div_area += $total_crop_div;
            $gps += $block->total_gps;

        }

        $data['rows'][] = [
            'block' => '<strong>Total</strong>',
            'gps' => $gps,
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
            'total_rfc' => $total_rfc_area,
            'total_crop_div' => $total_crop_div_area,

        ];
    }

    private function _allblocks($blocks, &$data)
    {

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_area = $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_rfc_area = $total_crop_div_area = 0;

        $data['rows'] = [];
        $gps = 0;
        // dd($blocks);
        // exit;

        foreach ($blocks as $block) {
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
            //            $total_non_ragi = $total_area - $total_ragi - $block->fc_area;

            //subtraction issue for float values
            $total_non_ragi = bcsub(bcsub($total_area, $total_ragi, 2), $block->fc_area, 2);
            $total_crop_div_area = $block->crop_div_ragi + $block->crop_div_non_ragi;
            $data['rows'][] = [
                'district' => $block->district,
                'block' => $block->block,
                'gps' => $block->total_gps,
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
                'total_rfc' => $block->rfc_area,
                'total_crop_div' => $total_crop_div_area,
                'total_area' => $total_area
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
            $total_rfc_area += $block->rfc_area;
            $total_crop_div_area += $total_crop_div_area;
            $total_total_area += $total_area;

            $gps += $block->total_gps;

        }

        $data['rows'][] = [
            'district' => '<strong>Total</strong>',
            'block' => '',
            'gps' => $gps,
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
            'total_rfc' => $total_rfc_area,
            'total_fc' => $total_fc_area,
            'total_crop_div' => $total_crop_div_area,
            'total_area' => $total_total_area
        ];
    }

    private function districts($blocks, &$data)
    {
        // printr($blocks);
        // exit;
        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = $total_rfc_area = $total_crop_div_area = 0;

        $data['rows'] = [];
        $gps = $tblocks = 0;
        foreach ($blocks as $block) {
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

            //            $total_non_ragi = $total_area - $total_ragi - $block->fc_area;

            //subtraction issue for float values
            $total_non_ragi = bcsub(bcsub($total_area, $total_ragi, 2), $block->fc_area, 2);
            $total_crop_div = $block->crop_div_ragi + $block->crop_div_non_ragi;

            $data['rows'][] = [
                'district' => $block->district,
                'blocks' => $block->total_blocks,
                'gps' => $block->total_gps,
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
                'total_rfc' => $block->rfc_area,
                'total_crop_div' => $total_crop_div,
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
            $total_total_area += $total_area;
            $total_rfc_area += $block->rfc_area;
            $total_crop_div_area += $total_crop_div;
            $gps += $block->total_gps;
            $tblocks += $block->total_blocks;

        }

        $data['rows'][] = [
            'district' => '<strong>Total</strong>',
            'blocks' => $tblocks,
            'gps' => $gps,
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
            'total_rfc' => $total_rfc_area,
            'total_crop_div' => $total_crop_div_area,

        ];
    }

    public function allblocks($action = '')
    {

        $data = [];

        $data['title'] = 'Area Coverage Blockwise Report';


        $cropsModel = new CropsModel();
        $data['years'] = getAllYears();
        $data['seasons'] = $this->acModel->getSeasons();

        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();

        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        if ($this->request->getGet('season')) {
            $data['current_season'] = $this->request->getGet('season');
        }

        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }

        $data['block_id'] = '';
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }

        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season']
        ];


        $blocks = $this->acModel->getByDistrictNew($filter);
        // printr($blocks);
        // exit;
        $this->_allblocks($blocks, $data);

        //
        $data['allblocks'] = true;

        $data['crop_practices'] = $this->acModel->getCropPractices();
        $crops = $cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }

        $data['blocks'] = [];
        if ($data['district_id']) {
            $data['blocks'] = (new BlockModel())->where('district_id', $data['district_id'])
                ->asArray()->findAll();
        }

        if ($action == 'download') {
            $data['fin_year'] = (new YearModel())->find($data['year_id'])->name;
            $data['table'] = view('Admin\Reports\Views\areacoverage_table', $data);
            $filename = 'AreaCoverageAllBlocksReport_' . $data['current_season'] . '_' . $data['fin_year'] . '_' . date('Y-m-d His') . '.xlsx';

            $spreadsheet = Export::createExcelFromHTML($data['table'], $filename, true);
            if ($spreadsheet) {
                $worksheet = $spreadsheet->getActiveSheet();
                $wordWrapCols = [
                    'A1',
                    'D1',
                    'E1',
                    'F1',
                    'G1',
                    'M2',
                    'O2',
                    'P2',
                    'Q2',
                    'R1',
                    'S1',
                    'T1',
                    'U1'
                ];
                foreach ($wordWrapCols as $col) {
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }

            exit;
        }

        $data['filter_panel'] = view('Admin\Reports\Views\areacoverage_filter_allblocks', $data);
        $data['download_url'] = admin_url('reports/areacoverage/allblocks/download');
        $data['get_blocks'] = Url::getBlocks;

        return $this->template->view('Admin\Reports\Views\areacoverage', $data);
    }
    public function allgps($action = '')
    {

        $data = [];

        $data['title'] = 'Area Coverage GPwise Report';


        $cropsModel = new CropsModel();
        $data['years'] = getAllYears();
        $data['seasons'] = $this->acModel->getSeasons();

        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();
        if ($this->user->district_id) {
            $data['district_id'] = $filter['district_id'] = $this->user->district_id;
            $data['districts'] = (new DistrictModel())->where('id', $this->user->district_id)->asArray()->find();
        } else {
            $data['districts'] = (new DistrictModel())->orderBy('name')->asArray()->find();
        }
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        if ($this->request->getGet('season')) {
            $data['current_season'] = $this->request->getGet('season');
        }

        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }

        $data['block_id'] = '';
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }


        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season'],
            'district_id' => $data['district_id'],
            'block_id' => $data['block_id'],
        ];


        $blocks = $this->acModel->getByBlockNew($filter);
        // printr($blocks);
        // exit;
        $this->gps($blocks, $data);

        //
        $data['allgps'] = true;

        $data['crop_practices'] = $this->acModel->getCropPractices();
        $crops = $cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }

        $data['blocks'] = [];
        if ($data['district_id']) {
            $data['blocks'] = (new BlockModel())->where('district_id', $data['district_id'])
                ->asArray()->findAll();
        }
        $districts = (new DistrictModel())->where('id', $this->request->getGet('district_id'))->first();
        $blocks = (new BlockModel())->where('district_id', $data['district_id'])
            ->asArray()->first();
        // printr($blocks);
        // exit;
        if ($action == 'download') {
            $data['fin_year'] = (new YearModel())->find($data['year_id'])->name;
            $data['table'] = view('Admin\Reports\Views\areacoverage_table_allgps', $data);
            $filename = 'AreaCoverageAllGPsReport__' . $data['fin_year'] . $data['current_season'];

            // Check if $districts->name exists before appending it to the filename
            if ($districts && isset($districts->name)) {
                $filename .= '_districts_' . $districts->name;
            }

            // Check if $blocks exists and if $blocks->name is set before appending it to the filename


            $filename .= '_' . date('Y-m-d His') . '.xlsx';


            $spreadsheet = Export::createExcelFromHTML($data['table'], $filename, true);
            if ($spreadsheet) {
                $worksheet = $spreadsheet->getActiveSheet();
                $wordWrapCols = [
                    'A1',
                    'D1',
                    'E1',
                    'F1',
                    'G1',
                    'M2',
                    'O2',
                    'P2',
                    'Q2',
                    'R1',
                    'S1',
                    'T1',
                    'U1'
                ];
                foreach ($wordWrapCols as $col) {
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }

            exit;
        }
        $params = 'year_id=' . $data['year_id'];
        $params .= '&season=' . $data['current_season'];
        $params .= '&district_id=' . $data['district_id'];
        $params .= '&block_id=' . $data['block_id'];
        $data['filter_panel'] = view('Admin\Reports\Views\areacoverage_filter_allgps', $data);
        $data['download_url'] = admin_url('reports/areacoverage/allgps/download?' . $params);
        $data['get_blocks'] = Url::getBlocks;

        return $this->template->view('Admin\Reports\Views\areacoverage_gpwise', $data);
    }

    public function getUploadStatus()
    {

        $data = [];



        $data['seasons'] = $this->acModel->getSeasons();

        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();

        $data['years'] = (new YearModel())->where('id', $data['year_id'])->asArray()->find();

        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        $week_dates = $this->acModel->getWeekDate();

        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $start_date = $this->request->getGet('start_date');
        } else if ($week_dates) {
            $data['start_date'] = $week_dates['start_date'];
        } else {
            $data['start_date'] = '';
        }

        if ($this->request->getGet('season')) {
            $data['current_season'] = $this->request->getGet('season');
        }

        $statuses = $this->acModel->getUploadStatus($data['start_date']);
        // printr($statuses);
        // exit;

        $data['statuses'] = [];
        $total_blocks = $total_ac_blocks = 0;
        foreach ($statuses as $status) {
            $data['statuses'][] = [
                'district' => $status->district,
                'total_blocks' => $status->total_blocks,
                'total_ac_blocks' => $status->total_ac_blocks,
                'remaining' => ($status->total_blocks - $status->total_ac_blocks),
            ];
            $total_blocks += $status->total_blocks;
            $total_ac_blocks += $status->total_ac_blocks;
        }
        $data['statuses'][] = [
            'district' => '<strong>Total</strong>',
            'total_blocks' => $total_blocks,
            'total_ac_blocks' => $total_ac_blocks,
            'remaining' => ($total_blocks - $total_ac_blocks),
        ];

        $weeks = $this->acModel->getWeeks();
        $data['weeks'] = [];
        $week_start_date = '';
        foreach ($weeks as $week) {
            //dropdown weeks
            if (strtotime($week['start_date']) <= strtotime('today')) {
                $data['weeks'][$week['start_date']] = $week_start_date = $week['start_date'];
            }
        }

        $data['week_start_date'] = $data['start_date'];

        return $this->template->view('Admin\Reports\Views\areacoverage_upload_status', $data);
    }

    public function blockWiseGetUploadStatus()
    {

        $data = [];
        $data['districts'] = $this->districtModel->getAll();
        // printr($data['districts']);
        // exit;
        $this->template->add_package(array('datatable', 'select2'), true);


        $data['seasons'] = $this->acModel->getSeasons();
        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();

        $data['years'] = (new YearModel())->where('id', $data['year_id'])->asArray()->find();
        $week_dates = $this->acModel->getWeekDate();
        $filter = array();

        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }


        if ($this->request->getGet('year_id')) {
            $filter['year_id'] = $this->request->getGet('year_id');
        }

        if ($this->request->getGet('start_date')) {
            $filter['start_date'] = $start_date = $this->request->getGet('start_date');
        } else if ($week_dates) {
            $filter['start_date'] = $week_dates['start_date'];
        } else {
            $filter['start_date'] = '';
        }

        if ($this->request->getGet('season')) {
            $filter['current_season'] = $this->request->getGet('season');
        }

        // print_r($filter);
        // exit;
        $blockstatuses = $this->acModel->getBlockWiseStatus($filter);
        // dd($blockstatuses);
        // exit;


        $blockstatuses = $this->acModel->getBlockWiseStatus($filter);


        $data['blockstatuses'] = [];
        foreach ($blockstatuses as $blockstatus) {
            $status = $blockstatus->status;
            if (!isset($status)) {
                $status = 3;
            }

            $actionTaken = '';
            if ($status == 0) {
                $actionTaken = '<label class="badge badge-' . $this->colors_ac[$status] . '">Pending</label>';
            } elseif ($status == 1) {
                $actionTaken = '<label class="badge badge-' . $this->colors_ac[$status] . '">Approved</label>';
            } elseif ($status == 2) {
                $actionTaken = '<label class="badge badge-' . $this->colors_ac[$status] . '">Rejected</label>';
            } elseif ($status == 3) {
                $actionTaken = '<label class="badge badge-' . $this->colors_ac[$status] . '">Not Uploaded</label>';
            }

            $data['blockstatuses'][] = [
                'district' => $blockstatus->district_name,
                'block' => $blockstatus->block_name,
                'status' => '<label class="badge badge-' . $this->colors_ac[$status] . '">' . $this->statuses[$status] . '</label>',
                'action_taken' => $actionTaken,

            ];

        }
        $weeks = $this->acModel->getWeeks();
        $data['weeks'] = [];
        $week_start_date = '';
        foreach ($weeks as $week) {
            //dropdown weeks
            if (strtotime($week['start_date']) <= strtotime('today')) {
                $data['weeks'][$week['start_date']] = $week_start_date = $week['start_date'];
            }
        }
        $data['filtered_data_url'] = admin_url('reports/areacoverage/blockWiseGetUploadStatus/filtered');

        $data['week_start_date'] = $filter['start_date'];

        return $this->template->view('Admin\Reports\Views\blockwise_areacoverage_upload_status', $data);
    }
}