<?php

namespace Api\Dashboard\Controllers;

use Api\Dashboard\Models\AreaCoverageModel;
use Api\Dashboard\Models\ReportsModel;
use Api\Dashboard\Models\YearModel;
use Api\Dashboard\Models\CurrentYearChartModel;
use CodeIgniter\RESTful\ResourceController;
use Api\Dashboard\Models\DistrictModel;
use Api\Dashboard\Models\CropsModel;
use Api\Dashboard\Models\BlockModel;
use CodeIgniter\API\ResponseTrait;

class Partnerdashboard extends ResourceController
{
    use ResponseTrait;
    private $apiModel;
    private $user;

    public function __construct()
    {
        $this->reportModel = new ReportsModel();
        $this->districtModel = new DistrictModel();
        $this->acModel = new AreaCoverageModel();
        helper("aio");
        $this->user = service('user');
        Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    }
    /**
     * This function is responsible for retrieving dashboard data card to the omm partner dashboard .
     * Implemented by Rakesh Nayak.
     */
    public function partnerdashboard()
    {

        $dataget = $this->request->getPost();
         // return $this->respond($dataget);
        $data = [];
        $chart_type = $this->request->getGet('chart_type');
        $year = $dataget['year_id'];
        $fund_agency_id = $dataget['fund_agency_id'];


        if ($chart_type == 'district') {
            $abstractDists = $this->reportModel->getDistrictwiseOpening([
                'year' => $year,
                'fund_agency_id' => $fund_agency_id
            ]);

            $xaxis = [];
            foreach ($abstractDists as $dist) {
                $xaxis[] = $dist->district;
            }
            $series_ex = $series_fr = [];
            foreach ($abstractDists as $dist) {
                $series_ex[] = in_lakh($dist->ex_total, '');
                $series_fr[] = in_lakh($dist->fr_total, '');
            }
            $data['xaxis'] = $xaxis;
            $data['series'] = [
                ['name' => 'Expense', 'data' => $series_ex],
                ['name' => 'Fund Receipt', 'data' => $series_fr]
            ];
            $data['year'] = getYear($year);

            //pie chart
            $data['piechart'] = [];
            foreach ($abstractDists as $abstractDist) {
                $fr_total = (float) $abstractDist->fr_total;
                if ($fr_total > 0) {
                    $data['piechart'][] = [
                        'name' => $abstractDist->district,
                        'value' => round(($abstractDist->ex_total / $fr_total) * 100, 2)
                    ];
                }
            }
        }

        if ($chart_type == 'block') {
            $abstractBlocks = $this->reportModel->getBlockwiseOpening([
                'year' => $year,
                'fund_agency_id' => $fund_agency_id,
                'district_id' => $dataget['district_id']
            ]);

            $xaxis = [];
            foreach ($abstractBlocks as $block) {
                $xaxis[] = $block->block;
            }
            $series_ex = $series_fr = [];
            foreach ($abstractBlocks as $block) {
                $series_ex[] = in_lakh($block->ex_total, '');
                $series_fr[] = in_lakh($block->fr_total, '');
            }
            $data['xaxis'] = $xaxis;
            $data['series'] = [
                ['name' => 'Expense', 'data' => $series_ex],
                ['name' => 'Fund Receipt', 'data' => $series_fr]
            ];
            $data['year'] = getYear($year);

            //pie chart
            $data['piechart'] = [];
            foreach ($abstractBlocks as $block) {
                $fr_total = (float) $block->fr_total;
                $data['piechart'][] = [
                    'name' => $block->block,
                    'value' => $fr_total ? round(($block->ex_total / $fr_total) * 100, 2) : 0
                ];
            }
        }

        //abstract
        $filter = [
            'year_id' => $year,
            'fund_agency_id' => $fund_agency_id
        ];
        if ($dataget['district_id']) {
            $filter['district_id'] = $dataget['district_id'];
        }
        if ($dataget['block_id']) {
            $filter['block_id'] = $dataget['block_id'];
        }

        if ($dataget['user_name']) {
            $filter['user_name'] = $dataget['user_name'];
        }

        // printr($filter); exit;
        $data['abstract'] = $this->abstract_data($filter);
        return $this->respond($data);
    }



    private function abstract_data($data)
    {
        $filter['user_name'] = $data['user_name'];

        $user_id = $this->reportModel->getUserIdByName($filter);
        // printr($filter['user_id']); exit;

        $filter = [
            'year' => $data['year_id'],
            'fund_agency_id' => $data['fund_agency_id'],
        ];
        if (isset($data['district_id'])) {
            $filter['district_id'] = $data['district_id'];
        }
        if (isset($data['block_id'])) {
            $filter['block_id'] = $data['block_id'];
            $filter['user_id'] = $user_id;
        }

        $abstract = $this->reportModel->getAbstractTotal($filter);

        $ob = $fr = $ex = $cb = 0;
        foreach ($abstract as $item) {
            $ob += $item->ob_total;
            $fr += $item->fr_total;
            $ex += $item->xp_total;
            $cb += $item->cb_total;
        }

        $data['ob'] = in_lakh($ob, '');
        $data['fr'] = in_lakh($fr, '');
        $data['ex'] = in_lakh($ex, '');
        $data['cb'] = in_lakh($cb, '');

        return $data;
    }



    /**
     * Generates the chart data from partnerdashboardAreacoverage function.
     *
     *
     */
    public function partnerdashboardAreacoverage(){
        $cropsModel = new CropsModel();
        $data = [];
        $dataget = $this->request->getPost();
        $data['years'] = getAllYears();
        $data['seasons'] = $this->acModel->getSeasons();

        $data['current_season'] = strtolower(getCurrentSeason());
         $data['year_id'] = getCurrentYearId();

        if (isset($dataget['year_id'])) {
            $data['year_id'] = $dataget['year_id'];
        }
        $data['current_season'] = 'rabi';
        if (isset($dataget['season'])) {
            $data['current_season'] = 'rabi';
        }

        $data['district_id'] = '';

        if (isset($dataget['district_id'])) {
            $data['district_id'] = $dataget['district_id'];
        }

        $data['block_id'] = '';

        if (isset($dataget['block_id'])) {
            $data['block_id'] = $dataget['block_id'];
        }

        $dates = $this->acModel->getWeekDate();

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

        if ($dataget['block_id']) {
            $filter['block_id'] = $dataget['block_id'];
        } else if ($dataget['district_id']) {
            $filter['district_id'] = $dataget['district_id'];
        }

        if ($dataget['block_id']) {
            $data['block_id'] = $filter['block_id'] = $dataget['block_id'];
            $data['districts'] = (new DistrictModel())->where('id', $dataget['block_id'])->asArray()->find();
        } else if ($dataget['district_id']) {
            $data['district_id'] = $filter['district_id'] = $dataget['district_id'];
            $data['districts'] = (new DistrictModel())->where('id', $dataget['district_id'])->asArray()->find();
        } else {
            $data['districts'] = (new DistrictModel())->orderBy('name')->asArray()->find();
        }

        $blocks = $this->acModel->getAreaCoverageReport($filter);



        if ($dataget['block_id']) {
            $this->gps($blocks, $data);
        } else if ($dataget['district_id']) {
            $this->blocks($blocks, $data);
        } else {
            $this->districts($blocks, $data);
        }

        $data['crop_practices'] = $this->acModel->getCropPractices();
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

        $weeks = $this->acModel->getWeeks();

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

        return $this->respond($data);


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
}
