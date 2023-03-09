<?php
namespace Admin\Common\Controllers;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Controllers\Reports;
use Admin\Reports\Models\ReportsModel;
use Admin\Transaction\Controllers\OpeningBalance;
use Admin\Transaction\Models\ClosingbalanceModel;
use Admin\Transaction\Models\FRCheckModel;
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
        } else {
            return $this->fund_receipt_check();
        }

        $data['fr_url'] = site_url(Url::transactionAdd) . '?month=' . getCurrentMonthId() . '&year=' . getCurrentYearId() . '&txn_type=fund_receipt&agency_type_id=' . $this->user->agency_type_id;

        $data['year'] = date('F') . ' ' . getYear(getCurrentYearId());

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

    protected function fund_receipt_check()
    {

        $frcModel = new FRCheckModel();

        if ($this->request->isAJAX()) {
            $choice = $this->request->getGet('choice');

            $data = [
                'month' => getCurrentMonthId(),
                'year' => getCurrentYearId(),
                'district_id' => $this->user->district_id,
                'block_id' => $this->user->block_id
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
            'year_upto' => getCurrentYearId(),
        ];

        $filter['transaction_type'] = 'fund_receipt';
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

        $data['components'] = [];


        $filter['transaction_type'] = 'fund_receipt';
        $filter['district_id'] = $this->user->district_id;
        $filter['fund_agency_id'] = $this->user->fund_agency_id;
        $data['abstracts'] = [];
        $data['fund_abstract'] = $fund_abstract = $reportModel->getTransactionAbstractDistrict($filter);
        //fund abstract loop for bar chart
        foreach ($fund_abstract as $key => $fund_abstracts) {

            $data['fund']['label'][] = $fund_abstracts->block;

            //$data['fund']['data'][] = in_lakh($fund_abstracts->total,'') ;

            $data['fund']['data'][$key]['value'] = in_lakh($fund_abstracts->total, '');
            //    $data['fund']['data'][$key]['itemStyle']['color'] = '#26c6da' ;

            $data['abstracts'][$fund_abstracts->block] = [
                'block' => $fund_abstracts->block,
                'ftotal' => in_lakh($fund_abstracts->total, ''),

            ];
        }

        $filter['transaction_type'] = 'expense';
        $filter['district_id'] = $this->user->district_id;
        $filter['fund_agency_id'] = $this->user->fund_agency_id;
        $data['expense_abstract'] = $expense_abstract = $reportModel->getTransactionAbstractDistrict($filter);

        // expense abstract loop for bar chart
        foreach ($expense_abstract as $key => $expense_abstracts) {

            $data['abstract']['label'][] = $expense_abstracts->block;

            $data['abstract']['data'][$key]['value'] = in_lakh($expense_abstracts->total);
            $data['abstracts'][$expense_abstracts->block]['etotal'] = in_lakh($expense_abstracts->total, '');


            $etotal = $expense_abstracts->total;
            $ftotal = $data['abstracts'][$expense_abstracts->block]['ftotal'];

            if ((int)$ftotal) {
                $uc = ($etotal / $ftotal) * 100;
            } else {
                $uc = 0;
            }
            $data['abstracts'][$expense_abstracts->block]['uc'] = in_lakh($uc);
        }
        usort($data['abstracts'], fn($a, $b) => $b['uc'] <=> $a['uc']);

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

        $data['pendingstatus'] += count($reportsModel->getPendingExpenses($filter));
        $data['pendingstatus'] += count($reportsModel->getPendingClosingBalance($filter));

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

            $uc = ($etotal / $ftotal) * 100;
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

        $this->pendingUploads($data);

        return $this->template->view('Admin\Common\Views\spmu_dashboard', $data);

    }

    public function spmu_dashboard_chart()
    {

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

}
