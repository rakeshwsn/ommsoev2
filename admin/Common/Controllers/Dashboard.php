<?php
namespace Admin\Common\Controllers;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Controllers\Reports;
use Admin\Reports\Models\ReportsModel;
use Admin\Transaction\Controllers\OpeningBalance;
use Admin\Transaction\Models\ClosingbalanceModel;
use Admin\Transaction\Models\FRCheckModel;
use Admin\Transaction\Models\MisctransactionModel;
use Admin\Transaction\Models\ORCheckModel;
use App\Controllers\AdminController;
use App\Traits\ReportTrait;
use App\Traits\TreeTrait;
use CodeIgniter\Config\View;
use Config\Url;

class Dashboard extends AdminController
{
    use TreeTrait, ReportTrait {
        ReportTrait::generateTable insteadof TreeTrait;
        ReportTrait::getTable insteadof TreeTrait;
    }
    private $reportModel;

    public function __construct()
    {
        $this->reportModel = new ReportsModel();
    }

    public function index()
    {
        $data = [];

        if (!$this->request->isAJAX()) {
            $data['fr_check'] = $this->receipt_check('fr');
            $data['or_check'] = $this->receipt_check('or');
        } else {
            return $this->receipt_check(); //only for ajax call
        }

        $data['fr_url'] = site_url(Url::transactionAdd) . '?month=' . getCurrentMonthId() . '&year=' . getCurrentYearId() . '&txn_type=fund_receipt&agency_type_id=' . $this->user->agency_type_id;
        $data['or_url'] = site_url(Url::otherReceiptAdd) . '?month=' . getCurrentMonthId() . '&year=' . getCurrentYearId() . '&txn_type=fund_receipt&agency_type_id=' . $this->user->agency_type_id;

        $data['abstract_url'] = admin_url('dashboard/getabstractdetails');

        $data['year'] = date('F') . ' ' . getYear(getCurrentYearId());

        $data['years'] = getAllYears();
        $data['year_id'] = getCurrentYearId();

        if ($this->request->getGet('year')) {
            $data['year_id'] = $this->request->getGet('year');
        }

        $data['fund_agency_id'] = 1;
        if ($this->request->getGet('fund_agency_id')) {
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        if ($this->user->agency_type_id == $this->settings->block_user) {
            return $this->fa_dashboard($data);
        }

        if ($this->user->agency_type_id == $this->settings->district_user) {
            return $this->atma_dashboard($data);
        }

        if ($this->user->agency_type_id == $this->settings->ps_user) {
            return $this->ps_dashboard($data);
        }

        if ($this->user->agency_type_id == $this->settings->spmu_user) {
            return $this->spmu_dashboard($data);
        }

        return $this->template->view('Admin\Common\Views\dashboard', $data);
    }



    public function chart()
    {
        $data = [];
        $chart_type = $this->request->getGet('chart_type');
        $year = $this->request->getGet('year');
        $fund_agency_id = $this->request->getGet('fund_agency_id') ?: $this->user->fund_agency_id;
        $filter = [
            'year' => $year,
            'fund_agency_id' => $fund_agency_id
        ];

        $Distsdata = $this->reportModel->getDistrictwiseOpening($filter);

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
            $filter['district_id'] = $this->user->district_id;
        }
        if ($this->user->block_id) {
            $filter['block_id'] = $this->user->block_id;
        }
        $data['abstract'] = $this->abstract_data($filter);

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($data, JSON_NUMERIC_CHECK);

    }

    public function getAbstractDetails()
    {
        $stmt = $this->request->getGet('stmt');
        $year = $this->request->getGet('year');

        $filter = [
            'year' => $year,
            'fund_agency_id' => 1,
        ];

        if ($this->user->district_id) {

        }

        if ($this->user->block_id) {

        }

        $abstract = $this->reportModel->getAbstractTotal($filter);

        $ob_total = 0;
        $fr_total = 0;
        $ex_total = 0;
        $cb_total = 0;
        $sl = 1;
        foreach ($abstract as $item) {
            $available_bal = $item->ob_total + $item->fr_total;
            $percentage = 0;
            if ($available_bal) {
                $percentage = ($item->xp_total / $available_bal) * 100;
            }

            if ($percentage >= 60) {
                $color = '#9eff9e';
            } elseif ($percentage >= 40) {
                $color = 'yellow';
            } elseif ($percentage >= 25) {
                $color = '#ffbd44';
            } else {
                $color = '#ff918f';
            }
            $data['rows'][] = [
                'agency' => $item->agency,
                'ob_in_lakh' => in_lakh($item->ob_total),
                'fr_in_lakh' => in_lakh($item->fr_total),
                'ex_in_lakh' => in_lakh($item->xp_total),
                'cb_in_lakh' => in_lakh($item->cb_total),
                'percentage' => round($percentage, 2),
                'total' => $item->ob_total,
                'bg_color' => $color,
            ];
            $ob_total += $item->ob_total;
            $fr_total += $item->fr_total;
            $ex_total += $item->xp_total;
            $cb_total += $item->cb_total;
        }
        usort($data['rows'], function ($a, $b) {
            return $a['percentage'] - $b['percentage'];
        });
        foreach ($data['rows'] as &$row) {
            $row['sl'] = $sl++;
        }

        $available_bal = $ob_total + $fr_total;
        $percentage = 0;
        if ($available_bal) {
            $percentage = ($ex_total / $available_bal) * 100;
        }
        $data['rows'][] = [
            'sl' => '',
            'agency' => 'Total',
            'ob_in_lakh' => in_lakh($ob_total),
            'fr_in_lakh' => in_lakh($fr_total),
            'ex_in_lakh' => in_lakh($ex_total),
            'cb_in_lakh' => in_lakh($cb_total),
            'percentage' => round($percentage, 2),
            'bg_color' => '',
        ];

        $data['html'] = view('\Admin\Common\Views\abstract_table', $data);

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($data);
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

    protected function receipt_check($check_type = '')
    {

        $frcModel = new FRCheckModel();
        $check_type = $this->request->getGet('check_type') ?: $check_type;
        $data = [
            'month' => getCurrentMonthId(),
            'year' => getCurrentYearId(),
            'district_id' => $this->user->district_id,
            'block_id' => $this->user->block_id,
            'fund_agency_id' => $this->user->fund_agency_id,
            'agency_type_id' => $this->user->agency_type_id,
            'check_type' => $check_type
        ];

        $fr = $frcModel->where($data)->first();

        if ($this->request->isAJAX()) {
            $choice = $this->request->getGet('choice');


            if ($choice == 'yes') {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }

            if (!$fr) {
                $frcModel->insert($data);
            }

            return $this->response->setJSON(['success' => true]);

        }

        return !$fr;
    }


    protected function fa_dashboard(&$data)
    {
        $this->reportModel = new ReportsModel();

        $filter = [
            'user_id' => $this->user->user_id,
            'block_id' => $this->user->block_id,
            'fund_agency_id' => $this->user->fund_agency_id,
            'year_upto' => getCurrentYearId(),
        ];

        //$filter['month'] = getMonthIdByMonth(date('m'));
        $filter['year'] = getCurrentYearId();


        $data['components'] = [];
        $data['chart_url'] = admin_url('dashboard/chart');
        return $this->template->view('Admin\Common\Views\fa_dashboard', $data);

    }

    protected function atma_dashboard(&$data)
    {
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');
        $this->reportModel = new ReportsModel();

        $filter = [
            'user_id' => $this->user->user_id,
            'district_id' => $this->user->district_id,
            'fund_agency_id' => $this->user->fund_agency_id,
            'year_upto' => getCurrentYearId(),
        ];

        //abstract
        $data['abstract'] = $this->abstract_data([
            'year_id' => $data['year_id'],
            'district_id' => $this->user->district_id,
            'fund_agency_id' => $data['fund_agency_id']
        ]);

        $filter['transaction_type'] = 'fund_receipt';


        $data['fr'] = $this->reportModel->getTransactionTotal($filter);

        $filter['user_id'] = null;
        $filter['district_id'] = $this->user->district_id;
        $filter['transaction_type'] = 'expense';
        $filter['agency_type_id'] = [5, 6, 7];
        $data['ex'] = $this->reportModel->getTransactionTotal($filter);

        $filter['month'] = getMonthIdByMonth(date('m'));
        $filter['year'] = getCurrentYearId();

        $data['cb'] = $data['fr'] - $data['ex'];
        //$data['cb'] = $this->reportModel->getClosingBalanceTotal($filter);

        $data['upload_status'] = $this->upload_status([
            'district_id' => $this->user->district_id
        ]);

        $this->pendingUploads($data);
        $data['chart_url'] = admin_url('dashboard/chart');

        return $this->template->view('Admin\Common\Views\atma_dashboard', $data);

    }

    protected function upload_status($filter = [])
    {

        $data = $this->getUploadStatus($filter);

        return view('Admin\Reports\Views\upload_status', $data);
    }

    private function pendingUploads(&$data)
    {
        $reportsModel = new ReportsModel();
        $data['pendingstatus'] = 0;

        $filter['year_id'] = getCurrentYearId();
        $filter['month_id'] = getCurrentMonthId();
        $filter['phase'] = [0, 1, 2];

        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }
        if ($this->user->fund_agency_id) {
            $filter['fund_agency_id'] = $this->user->fund_agency_id;
        }

        $data['pendingstatus'] += count($reportsModel->getPendingStatuses($filter));

        $data['pendingstatus_url'] = admin_url('pendingstatus');
    }

    protected function ps_dashboard(&$data)
    {
        $this->reportModel = new ReportsModel();

        $filter = [
            'user_id' => $this->user->user_id,
            'year_upto' => getCurrentYearId(),
        ];

        $filter['transaction_type'] = 'fund_receipt';
        $data['fr'] = $this->reportModel->getTransactionTotal($filter);

        $filter['transaction_type'] = 'expense';
        $data['ex'] = $this->reportModel->getTransactionTotal($filter);

        $filter['month'] = getMonthIdByMonth(date('m'));
        $filter['year'] = getCurrentYearId();

        $data['cb'] = $this->reportModel->getClosingBalanceTotal($filter);

        $data['components'] = [];
        $filter = [
            'user_id' => $this->user->user_id,
            'month_id' => getMonthIdByMonth(date('m')),
            'year_id' => getCurrentYearId(),
            'user_group' => [$this->settings->ps_user]
        ];
        $filter['block_users'] = [5, 6];
        $filter['block_user'] = false;
        if ($this->user->agency_type_id == $this->settings->block_user) {
            $filter['block_user'] = true;
        }

        return $this->template->view('Admin\Common\Views\ps_dashboard', $data);

    }

    protected function spmu_dashboard(&$data)
    {

        //abstract --rakesh--niranjan
        $data['abstract'] = $this->abstract_data([
            'year_id' => $data['year_id'],
            'fund_agency_id' => $data['fund_agency_id']
        ]);

        $data['fund_agencies'] = (new BlockModel())->getFundAgencies();

        $data['chart_url'] = admin_url('dashboard/chart');

        return $this->template->view('Admin\Common\Views\spmu_dashboard', $data);

    }

}