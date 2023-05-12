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

    public function index()
    {
        $data = [];

        if (!$this->request->isAJAX()) {
            $data['fr_check'] = $this->fund_receipt_check();
            $data['or_check'] = $this->other_receipt_check();
        } else {
            return $this->fund_receipt_check();
        }

        $data['fr_url'] = site_url(Url::transactionAdd) . '?month=' . getCurrentMonthId() . '&year=' . getCurrentYearId() . '&txn_type=fund_receipt&agency_type_id=' . $this->user->agency_type_id;
        $data['or_url'] = site_url(Url::otherReceiptAdd) . '?month=' . getCurrentMonthId() . '&year=' . getCurrentYearId() . '&txn_type=fund_receipt&agency_type_id=' . $this->user->agency_type_id;

        $data['year'] = date('F') . ' ' . getYear(getCurrentYearId());

        $data['years'] = getAllYears();
        $data['year_id'] = getCurrentYearId();

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

    public function chart() {
        $reportModel = new ReportsModel();
        $data = [];
        $chart_type = $this->request->getGet('chart_type');
        if($chart_type=='district'){
            $year = $this->request->getGet('year');
            $abstractDists = $reportModel->getTransactionAbstractDistrict(['year'=>$year]);

            $xaxis = [];
            foreach ($abstractDists as $dist) {
                $xaxis[] = $dist->district;
            }
            $series_ex = $series_fr = [];
            foreach ($abstractDists as $dist) {
                $series_ex[] = in_lakh($dist->ex_total,'');
                $series_fr[] = in_lakh($dist->fr_total,'');
            }
            $data['xaxis'] = $xaxis;
            $data['series'] = [
                ['name' => 'Expense','data' => $series_ex],
                ['name' => 'Fund Receipt','data' => $series_fr]
            ];
            $data['year'] = getYear($year);
        }

        if($chart_type=='agency'){

        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode( $data,JSON_NUMERIC_CHECK );

    }

    protected function fund_receipt_check()
    {

        $frcModel = new FRCheckModel();

        if ($this->request->isAJAX()) {
            $choice = $this->request->getGet('choice');
            $check_type = $this->request->getGet('check_type');

            $data = [
                'month' => getCurrentMonthId(),
                'year' => getCurrentYearId(),
                'district_id' => $this->user->district_id,
                'block_id' => $this->user->block_id,
                'fund_agency_id' => $this->user->fund_agency_id,
                'check_type' => 'fr'
            ];
            if ($choice == 'yes') {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            if ($check_type == 'or') {
                $data['check_type'] = 'or';
            } else {
                $data['check_type'] = 'fr';
            }

            $frcModel->insert($data);

            return $this->response->setJSON(['success' => true]);

        }

        $where = [
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'month' => getCurrentMonthId(),
            'year' => getCurrentYearId(),
        ];

        $fr = $frcModel->where($where)->first();
//echo $frcModel->db->getLastQuery();exit;
        return !$fr;
    }

    protected function other_receipt_check()
    {

        $frcModel = new ORCheckModel();

        if ($this->request->isAJAX()) {
            $choice = $this->request->getGet('choice');

            $data = [
                'month' => getCurrentMonthId(),
                'year' => getCurrentYearId(),
                'district_id' => $this->user->district_id,
                'block_id' => $this->user->block_id,
                'fund_agency_id' => $this->user->fund_agency_id,
                'check_type' => 'or'
            ];
            if ($choice == 'yes') {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }

            $frcModel->insert($data);

            return $this->response->setJSON(['success' => true]);

        }

        $where = [
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'month' => getCurrentMonthId(),
            'year' => getCurrentYearId(),
        ];

        $fr = $frcModel->where($where)->first();

        return !$fr;
    }

    protected function fa_dashboard(&$data)
    {
        $reportModel = new ReportsModel();

        $filter = [
            'user_id' => $this->user->user_id,
            'block_id' => $this->user->block_id,
            'year_upto' => getCurrentYearId(),
        ];

//        $data['ob'] = $reportModel->getOpeningBalanceTotal($filter);

        $filter['transaction_type'] = 'fund_receipt';
        $data['fr'] = $reportModel->getTransactionTotal($filter);

        $filter['transaction_type'] = 'expense';
        $data['ex'] = $reportModel->getTransactionTotal($filter);

        $filter['month'] = getMonthIdByMonth(date('m'));
        $filter['year'] = getCurrentYearId();

        $data['cb'] = $reportModel->getClosingBalanceTotal($filter);
        /*
                $reportModel = new ReportsModel();
                $data['components'] = [];
                $filter = [
                    'user_id' => $this->user->user_id,
                    'block_id' => $this->user->block_id,
                    'month_id' => getMonthIdByMonth(date('m')),
                    'year_id' => getCurrentYearId(),
                    'agency_type_id' => [5]
                ];
                $filter['block_users'] = [5,6];
                $filter['block_user'] = false;
                if($this->user->agency_type_id==$this->settings->block_user){
                    $filter['block_user'] = true;
                }
                $filter['fund_agency_id'] =  $this->user->fund_agency_id;

                $components = $reportModel->getMpr($filter);
                $components = $this->buildTree($components,'parent','component_id');

                $data['components'] = $this->getTable($components,'view');
        */
        $data['components'] = [];
        return $this->template->view('Admin\Common\Views\fa_dashboard', $data);

    }

    protected function atma_dashboard(&$data)
    {

        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');
        $reportModel = new ReportsModel();

        $filter = [
            'user_id' => $this->user->user_id,
            'district_id' => $this->user->district_id,
            'fund_agency_id' => $this->user->fund_agency_id,
            'year_upto' => getCurrentYearId(),
        ];

        $filter['transaction_type'] = 'fund_receipt';
        $filter['fund_agency_id'] = $this->user->fund_agency_id;

        $data['fr'] = $reportModel->getTransactionTotal($filter);

        $filter['user_id'] = null;
        $filter['district_id'] = $this->user->district_id;
        $filter['transaction_type'] = 'expense';
        $filter['agency_type_id'] = [5, 6, 7];
        $data['ex'] = $reportModel->getTransactionTotal($filter);

        $filter['month'] = getMonthIdByMonth(date('m'));
        $filter['year'] = getCurrentYearId();

        $data['cb'] = $data['fr'] - $data['ex'];
        //$data['cb'] = $reportModel->getClosingBalanceTotal($filter);

        $data['upload_status'] = $this->upload_status([
            'district_id' => $this->user->district_id
        ]);

        $this->pendingUploads($data);

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
        $reportModel = new ReportsModel();

        $filter = [
            'user_id' => $this->user->user_id,
            'year_upto' => getCurrentYearId(),
        ];

//        $data['ob'] = $reportModel->getOpeningBalanceTotal($filter);

        $filter['transaction_type'] = 'fund_receipt';
        $data['fr'] = $reportModel->getTransactionTotal($filter);

        $filter['transaction_type'] = 'expense';
        $data['ex'] = $reportModel->getTransactionTotal($filter);

        $filter['month'] = getMonthIdByMonth(date('m'));
        $filter['year'] = getCurrentYearId();

        $data['cb'] = $reportModel->getClosingBalanceTotal($filter);

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
//        $reportModel = new ReportsModel();
//        $components = $reportModel->getMpr($filter);
//        $components = $this->buildTree($components,'parent','component_id');

//        $data['components'] = $this->getTable($components,'view');

        return $this->template->view('Admin\Common\Views\ps_dashboard', $data);

    }

    protected function spmu_dashboard(&$data)
    {
        $reportModel = new ReportsModel();

        $filter = [
            'agency_type_id' => [7, 8, 9],
            'fund_agency_id' => 1,
            'year_upto' => getCurrentYearId(),
        ];

        $filter['transaction_type'] = 'fund_receipt';
        $data['fr'] = $reportModel->getTransactionTotal($filter);

        $filter['transaction_type'] = 'expense';
        $filter['agency_type_id'] = [5, 6, 7, 8, 9];
        $data['ex'] = $reportModel->getTransactionTotal($filter);

        $data['cb'] = $data['fr'] - $data['ex'];

        $data['fr'] = in_lakh($data['fr'],'');
        $data['ex'] = in_lakh($data['ex'],'');
        $data['cb'] = in_lakh($data['cb'],'');

        $data['chart_url'] = admin_url('dashboard/chart');

        return $this->template->view('Admin\Common\Views\spmu_dashboard', $data);

    }

    protected function spmu_dashboard_old(&$data)
    {
        $reportModel = new ReportsModel();

        $filter = [
//            'user_id' => $this->user->user_id,
//            'district_id' => $this->user->district_id,
            'agency_type_id' => [7, 8, 9],
            'fund_agency_id' => 1,
            'year_upto' => getCurrentYearId(),
        ];

        $filter['transaction_type'] = 'fund_receipt';
        $data['fr'] = $reportModel->getTransactionTotal($filter);

        $filter['transaction_type'] = 'expense';
        $filter['agency_type_id'] = [5, 6, 7, 8, 9];
        $data['ex'] = $reportModel->getTransactionTotal($filter);

        $data['cb'] = $data['fr'] - $data['ex'];


        $filter['transaction_type'] = 'fund_receipt';

        $data['abstracts'] = [];
        $data['fund_abstract'] = $fund_abstract = $reportModel->getTransactionAbstract($filter);

        //fund abstract loop for bar chart

        foreach ($fund_abstract as $key => $fund_abstracts) {

            $data['fund']['label'][] = $fund_abstracts->district;

            $data['fund']['data'][$key]['value'] = in_lakh($fund_abstracts->total, '');
            //    $data['fund']['data'][$key]['itemStyle']['color'] = '#26c6da' ;

            $data['abstracts'][$fund_abstracts->district] = [
                'district' => $fund_abstracts->district,
                'ftotal' => in_lakh($fund_abstracts->total, ''),

            ];

        }

        $filter['transaction_type'] = 'expense';
        $data['expense_abstract'] = $expense_abstract = $reportModel->getTransactionAbstract($filter);


        // expense abstract loop for bar chart
        foreach ($expense_abstract as $key => $expense_abstracts) {

            $data['abstract']['label'][] = $expense_abstracts->district;

            $data['abstract']['data'][$key]['value'] = in_lakh($expense_abstracts->total);

            $data['abstracts'][$expense_abstracts->district]['etotal'] = in_lakh($expense_abstracts->total, '');


            $etotal = $expense_abstracts->total;
            $ftotal = $data['abstracts'][$expense_abstracts->district]['ftotal'];
            $uc = 0;
            if((int)$ftotal>0){
                $uc = ($etotal / $ftotal) * 100;
            }
            $data['abstracts'][$expense_abstracts->district]['uc'] = in_lakh($uc);


        }

        usort($data['abstracts'], fn($a, $b) => $b['uc'] <=> $a['uc']);

         $filter['transaction_type'] = 'closing_balance';
         $data['closing_abstract'] = $closing_abstract = $reportModel->getTransactionAbstract($filter);
         // closing abstract loop for bar chart
         foreach ($closing_abstract as $closing_abstracts) {

             $data['closing']['label'][] = $closing_abstracts->district;

             $data['closing']['data'][] = in_lakh($closing_abstracts->cb);
         }

        return $this->template->view('Admin\Common\Views\spmu_dashboard_old', $data);

    }

    public function spmu_dashboard_chart(){

        $reportModel = new ReportsModel();
        if ($_GET['data'] == 'all') {

            $filter['transaction_type'] = 'fund_receipt';

            $data['abstracts'] = [];
            $fund_abstract = $reportModel->getTransactionAbstract($filter);

            foreach ($fund_abstract as $key => $fund_abstracts) {

                $data['fund']['label'][] = $fund_abstracts->district;

                $data['fund']['data'][] = in_lakh($fund_abstracts->total, '');

                $data['abstracts'][$fund_abstracts->district] = [
                    'district' => $fund_abstracts->district,
                    'ftotal' => in_lakh($fund_abstracts->total, ''),
                ];

            }

            $filter['transaction_type'] = 'expense';
            $expense_abstract = $reportModel->getTransactionAbstract($filter);

            foreach ($expense_abstract as $key => $expense_abstracts) {

                $data['abstract']['label'][] = $expense_abstracts->district;

                $data['abstract']['data'][] = in_lakh($expense_abstracts->total);


                $etotal = $expense_abstracts->total;
                $ftotal = $data['abstracts'][$expense_abstracts->district]['ftotal'];

                $uc = ($etotal / $ftotal) * 100;
                $data['abstracts'][$expense_abstracts->district]['uc'] = in_lakh($uc);

            }
            usort($data['abstracts'], fn($a, $b) => $b['uc'] <=> $a['uc']);

		}

        if ($_GET['data'] == 'agency') {

            $filter['transaction_type'] = 'fund_receipt';

            $fund_abstract = $reportModel->getTransactionAbstractAgency($filter);
            foreach ($fund_abstract as $key => $fund_abstracts) {

                $data['fund']['label'][] = $fund_abstracts->agency;

                $data['fund']['data'][] = in_lakh($fund_abstracts->total, '');
            }

            $filter['transaction_type'] = 'expense';
            $expense_abstract = $reportModel->getTransactionAbstractAgency($filter);
            // expense abstract loop for bar chart
            foreach ($expense_abstract as $key => $expense_abstracts) {

                $data['abstract']['label'][] = $expense_abstracts->agency;

                $data['abstract']['data'][] = in_lakh($expense_abstracts->total, '');

            }
        }
        if ($_GET['data'] == 'percentage') {

            $filter['transaction_type'] = 'fund_receipt';
            $data['abstracts'] = [];
            $fund_abstract = $reportModel->getTransactionAbstract($filter);
            $fabs = [];
            foreach ($fund_abstract as $key => $fund_abstracts) {
                $data['abstracts']['district'][] = $fund_abstracts->district;
                $fabs[$fund_abstracts->district] = in_lakh($fund_abstracts->total, '');
            }

            $filter['transaction_type'] = 'expense';
            $expense_abstract = $reportModel->getTransactionAbstract($filter);
            // expense abstract loop for bar chart
            foreach ($expense_abstract as $key => $expense_abstracts) {

                $etotal = $expense_abstracts->total;
                $ftotal = $fabs[$expense_abstracts->district];
                $uc = ($etotal / $ftotal) * 100;
                $data['abstracts']['uc'][] = in_lakh($uc);
            }
        }
        if ($_GET['data'] == 'district') {

            $filter['transaction_type'] = 'fund_receipt';

            $data['abstracts'] = [];
            $fund_abstract = $reportModel->getTransactionAbstract($filter);

            foreach ($fund_abstract as $key => $fund_abstracts) {

                $data['fund']['label'][] = $fund_abstracts->district;

                $data['fund']['data'][] = in_lakh($fund_abstracts->total, '');

                $data['abstracts'][$fund_abstracts->district] = [
                    'district' => $fund_abstracts->district,
                    'ftotal' => in_lakh($fund_abstracts->total, ''),
                ];

            }

            $filter['transaction_type'] = 'expense';
            $expense_abstract = $reportModel->getTransactionAbstract($filter);

            foreach ($expense_abstract as $key => $expense_abstracts) {

                $data['abstract']['label'][] = $expense_abstracts->district;

                $data['abstract']['data'][] = in_lakh($expense_abstracts->total);


                $etotal = $expense_abstracts->total;
                $ftotal = $data['abstracts'][$expense_abstracts->district]['ftotal'];

                $uc = ($etotal / $ftotal) * 100;
                $data['abstracts'][$expense_abstracts->district]['uc'] = in_lakh($uc);

            }
            usort($data['abstracts'], fn($a, $b) => $b['uc'] <=> $a['uc']);
		}

        echo json_encode($data);

    }

    public function getSpmuChart() {
        $reportModel = new ReportsModel();
        $chart_type = $this->request->getGet('chart_type');
        $data = [];

        $filter = [
            'fund_agency_id' => 1, //state plan
            'year' => 1
        ];
        $result = $reportModel->getAgencywiseAbstract($filter);

        if($chart_type=='all'){
            foreach ($result as &$item) {
                $item['fund_receipt'] = round(($item['fund_receipt']/100000),2);
                $item['expense'] = round(($item['expense']/100000),2);
            }
        }
        if($chart_type=='percentage'){
            foreach ($result as &$item) {
                $item['percentage'] = round((($item['expense']/$item['fund_receipt']) * 100),2);
            }
        }

        if($result){
            $data['status'] = true;
            $data['data'] = $result;
        } else {
            $data['status'] = false;
            $data['data'] = [];
        }

        return $this->response->setContentType('application/json')
            ->setJSON($data);
    }

}
