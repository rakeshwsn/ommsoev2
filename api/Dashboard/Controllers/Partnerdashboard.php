<?php

namespace Api\Dashboard\Controllers;


use Api\Dashboard\Models\ReportsModel;
use Api\Dashboard\Models\YearModel;
use Api\Dashboard\Models\CurrentYearChartModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Partnerdashboard extends ResourceController
{
    use ResponseTrait;
    private $apiModel;
    private $user;

    public function __construct()
    {
        $this->reportModel = new ReportsModel();
        helper("aio");
        $this->user = service('user');
        Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    }
    /**
     * This function is responsible for retrieving dashboard data card the omm partner dashboard .
     * Implemented by Rakesh Nayak.
     */
    public function partnerdashboard()
    {
        $data = [];
        $yearmodel = new YearModel();
        $chart_type = $this->request->getGet('chart_type');
        $year = 2;
        // echo $year; exit;
        $fund_agency_id = $this->request->getGet('fund_agency_id');


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
                'district_id' => $this->user->district_id
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
        if ($this->user->district_id) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }
        if ($this->user->block_id) {
            $filter['block_id'] = $this->request->getGet('block_id');
        }

        // printr($filter); exit;
        $data['abstract'] = $this->abstract_data($filter);
        return $this->respond($data);
    }



    private function abstract_data($data)
    {

        $filter = [
            'year' => $data['year_id'],
            'fund_agency_id' => $data['fund_agency_id'],
        ];
        if (isset($data['district_id'])) {
            $filter['district_id'] = $data['district_id'];
        }
        if (isset($data['block_id'])) {
            $filter['block_id'] = $data['block_id'];
            $filter['user_id'] = $this->user->user_id;
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
}
