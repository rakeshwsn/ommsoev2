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
    private $areaCoverageModel;
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
        $this->areaCoverageModel = new AreaCoverageModel();
    }

    public function index($action = '')
    {

        $data = [];
        $data['title'] = 'Area Coverage Report';

        $acModel = new AreaCoverageModel();
        $cropsModel = new CropsModel();
        $data['years'] = getAllYears();
        $data['seasons'] = $acModel->getSeasons();

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

        $dates = $acModel->getWeekDate();

        $data['start_date'] = '';
        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $this->request->getGet('start_date');
        }

        $filter = [
            'year_id' => $data['year_id'],
            'season' => $data['current_season'],
            'start_date' => $data['start_date']
        ];

        if ($this->request->getGet('start_date')) {
            $filter['start_date'] = $data['start_date'];
        }

        if ($data['block_id']) {
            $filter['block_id'] = $data['block_id'];
        } else if ($data['district_id']) {
            $filter['district_id'] = $data['district_id'];
        }

        if ($this->user->block_id) {
            $data['block_id'] = $filter['block_id'] = $this->user->block_id;
            $data['districts'] = (new DistrictModel())->where('id', $this->user->district_id)->asArray()->find();
        } else if ($this->user->district_id) {
            $data['district_id'] = $filter['district_id'] = $this->user->district_id;
            $data['districts'] = (new DistrictModel())->where('id', $this->user->district_id)->asArray()->find();
        } else {
            $data['districts'] = (new DistrictModel())->orderBy('name')->asArray()->find();
        }

        $blocks = $acModel->getAreaCoverageReport($filter);

        if ($data['block_id']) {
            $this->gps($blocks, $data);
        } else if ($data['district_id']) {
            $this->blocks($blocks, $data);
        } else {
            $this->districts($blocks, $data);
        }

        $data['crop_practices'] = $acModel->getCropPractices();
        $crops = $cropsModel->findAll();

        $data['crops'] = [];
        foreach ($crops as $crop) {
            $data['crops'][$crop->id] = $crop->crops;
        }

        $data['blocks'] = [];
        if ($data['district_id']) {
            $data['blocks'] = (new BlockModel())->where('district_id', $data['district_id'])
                ->orderBy('name')->asArray()->findAll();
        }

        if ($action == 'download') {
            $data['fin_year'] = (new YearModel())->find($data['year_id'])->name;
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

        $weeks = $acModel->getWeeks();

        $data['weeks'][0] = 'All weeks';
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

        return $this->template->view('Admin\Reports\Views\areacoverage', $data);
    }

    private function gps($blocks, &$data)
    {
        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

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

            $data['rows'][] = [
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
            $total_total_area += $total_area;

        }

        $data['rows'][] = [
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
            'total_area' => $total_total_area
        ];
    }

    private function blocks($blocks, &$data)
    {

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

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
            $total_total_area += $total_area;

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
            'total_area' => $total_total_area
        ];
    }

    private function _allblocks($blocks, &$data)
    {

        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

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
            'total_fc' => $total_fc_area,
            'total_area' => $total_total_area
        ];
    }

    private function districts($blocks, &$data)
    {
        $total_farmers_covered = $total_nursery_raised = $total_balance_smi =
            $total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
            $total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
            $total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
            $total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

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
            $total_total_area += $total_area;

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
            'total_area' => $total_total_area
        ];
    }

    public function allblocks($action = '')
    {

        $data = [];

        $data['title'] = 'Area Coverage Blockwise Report';

        $acModel = new AreaCoverageModel();
        $cropsModel = new CropsModel();
        $data['years'] = getAllYears();
        $data['seasons'] = $acModel->getSeasons();

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

        $blocks = $acModel->getByDistrictNew($filter);


        $this->_allblocks($blocks, $data);

        //
        $data['allblocks'] = true;

        $data['crop_practices'] = $acModel->getCropPractices();
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

                /*
                // Get the highest row index in the column
                $highestRow = $worksheet->getHighestRow();

                // Apply word wrap to each cell in column B
                $columnIndex = 'B'; // Change this to the desired column index
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cell = $worksheet->getCell($columnIndex . $row);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                $worksheet->getColumnDimension($columnIndex)->setWidth(20);*/

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

    public function getUploadStatus()
    {
        $data = [];

        $acModel = new AreaCoverageModel();

        $data['seasons'] = $acModel->getSeasons();

        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();

        $data['years'] = (new YearModel())->where('id', $data['year_id'])->asArray()->find();

        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        $week_dates = $acModel->getWeekDate();

        if ($this->request->getGet('start_date')) {
            $data['start_date'] = $start_date = $this->request->getGet('start_date');
        } else {
            $data['start_date'] = $week_dates['start_date'];
        }

        if ($this->request->getGet('season')) {
            $data['current_season'] = $this->request->getGet('season');
        }

        $statuses = $acModel->getUploadStatus($data['start_date']);
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

        $weeks = $acModel->getWeeks();
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

        $this->template->add_package(array('datatable', 'select2'), true);
        $acModel = new AreaCoverageModel();

        $data['seasons'] = $acModel->getSeasons();
        $data['current_season'] = strtolower(getCurrentSeason());
        $data['year_id'] = getCurrentYearId();

        $data['years'] = (new YearModel())->where('id', $data['year_id'])->asArray()->find();
        $week_dates = $acModel->getWeekDate();
        $filter = array();
        // print_r($filter);
        // exit;

        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }


        if ($this->request->getGet('year_id')) {
            $filter['year_id'] = $this->request->getGet('year_id');
        }

        if ($this->request->getGet('start_date')) {
            $filter['start_date'] = $start_date = $this->request->getGet('start_date');
        } else {
            $filter['start_date'] = $week_dates['start_date'];
        }
        if ($this->request->getGet('season')) {
            $filter['current_season'] = $this->request->getGet('season');
        }
        // print_r($filter);
        // exit;
        $blockstatuses = $this->areaCoverageModel->getBlockWiseStatus($filter);
        // dd($blockstatuses);
        // exit;

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
        $weeks = $acModel->getWeeks();
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