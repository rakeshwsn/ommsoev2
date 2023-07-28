<?php

namespace Admin\Transaction\Controllers;

use Admin\Common\Models\AllowuploadModel;
use Admin\Common\Models\CommonModel;
use Admin\Common\Models\YearModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Transaction\Models\ClosingbalanceModel;
use Admin\Transaction\Models\MisctransactionModel;
use Admin\Transaction\Models\MisctxnamtModel;
use Admin\Users\Models\UserGroupModel;
use App\Controllers\AdminController;
use Config\Url;

class OtherReceipt extends AdminController
{
//    use CommonTrait;
    private $txnModel,$cbModel;

    public function __construct() {
        $this->txnModel = new MisctransactionModel();
        $this->cbModel = new ClosingbalanceModel();
    }

    public function index() {

        $this->template->add_package(['datatable','uploader','jquery_loading'],true);
        $data['months'] = getMonths();
        $data['years'] = (new YearModel())->where('id',getCurrentYearId())->asArray()->findAll();
        $data['year'] = getCurrentYearId();
        $data['month'] = getCurrentMonthId();

        $data['datatable_url'] = Url::otherReceiptSearch;
        $data['add_url'] = Url::otherReceiptAdd;

        $data['message'] = $this->session->getFlashdata('message');

        $data['agency_type_id'] = $this->user->agency_type_id;
        $data['agency_types'] = [];
        if($this->user->agency_type_id==$this->settings->block_user) {
            $data['agency_types'] = (new UserGroupModel())->getBlockUsers();
        }

        return $this->template->view('Admin\Transaction\Views\otherreceipt', $data);
    }

    public function search() {
        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }
        $requestData = $_REQUEST;
        //$totalData = [];
        $totalData = $this->txnModel->getTotal(['user_id' => $this->user->user_id]);
        $totalFiltered = $totalData;

        $filter_search = $requestData['search']['value'];

        $order_columns = array(
            'month','year','date_added'
        );
        $filter_data = array(
            'user_id' => $this->user->user_id,
            'filter_search' => $filter_search,
            'order' => $requestData['order'][0]['dir'],
            'sort' => $order_columns[$requestData['order'][0]['column']],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );
//        $totalFiltered = 0;
        $totalFiltered = $this->txnModel->getTotal($filter_data);

        $filteredData = $this->txnModel->getAll($filter_data);

        $datatable=array();

        foreach($filteredData as $result) {

            $action  = '<div class="btn-group">';
            $action .= '<a class="btn btn-sm btn-primary btn-edit" href="' . Url::otherReceiptEdit. '/'.$result->id.'"><i class="fa fa-pencil"></i></a>';
            $action .= '</div>';

            $datatable[]=array(
                $result->month,
                $result->year,
                $result->agency_type,
                $result->fund_agency,
                ymdToDmy($result->created_at),
                $result->credit ,
                $result->debit,
                '<label class="badge badge-'.$this->colors[$result->status].'">'.$this->statuses[$result->status].'</label>',
                $action
            );

        }
        //printr($datatable);
        $json_data = array(
            "draw"            => isset($requestData['draw']) ? intval( $requestData['draw'] ):1,
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $datatable
        );

        return $this->response->setJSON($json_data);

    }

    public function add() {
        $this->error="";
        $json_data=[];
        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }

        $month = $this->request->getGet('month');
        $year = getCurrentYearId();
        if($this->request->getGet('year')){
            $year = $this->request->getGet('year');
        }

        $agency_type_id = $this->user->agency_type_id;
        if($this->request->getGet('agency_type_id')) {
            $agency_type_id = $this->request->getGet('agency_type_id');
        }

        $fund_agency_id = $this->user->fund_agency_id;

        $json_data = [
            'status' => false,
        ];

        //validate if exists
        $condition = [
            'month' => $month,
            'year' => $year,
            'agency_type_id' => $agency_type_id,
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'fund_agency_id' => $fund_agency_id,
            'user_id' => $this->user->user_id,
        ];

        $this->_validate($condition);

        if($this->request->getMethod(1)=='POST'){
            $txn_id = $this->txnModel
                ->insert([
                    'month'=>$month,
                    'year'=>$year,
                    'block_id' => $this->user->block_id,
                    'agency_type_id' => $agency_type_id,
                    'fund_agency_id' => $fund_agency_id,
                    'district_id' => $this->user->district_id,
                    'user_id' => $this->user->user_id,
                    'status' => (int)in_array($this->user->agency_type_id,$this->settings->auto_approve_users),
                ]);

            $txnAmts = [];
            foreach ($this->request->getPost('misc') as $key => $item) {
                $txnAmts[] = [
                    'txn_id' => $txn_id,
                    'head_id' => $key,
                    'amount' => $item
                ];
            }
            $txnAmtModel = new MisctxnamtModel();
            $txnAmtModel->insertBatch($txnAmts);
            $json_data = [
                'status' => true,
            ];
            $this->session->setFlashData('message','Other receipt added.');

        } 

        if(!$this->error) {
            $json_data = [
                'status' => true,
                'title' => 'Other receipt for ' . getMonthById($month)['name'] . ' ' . getCurrentYear(),
                'html' => $this->getForm()
            ];
        }else{
            $json_data = [
                'status' => false,
                'message' => $this->error
            ];
        }

        return $this->response->setJSON($json_data);

    }

    public function edit($id) {
        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }
        $json_data = [];
        $txn = $this->txnModel->find($id);

        if(!$txn){
            $json_data = [
                'status' => false,
                'message' => 'Other receipt doesn\'t exists',
            ];
        } else {
            //update
            if($this->request->getMethod(1)=='POST'){
                //validate
                if($txn->status==1){
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Cannot edit other receipt',
                    ]);
                }
                //delete exiting
                $this->txnModel->where(['id'=>$txn->id])->delete();
                $txnAmtModel = new MisctxnamtModel();
                $txnAmtModel->where(['txn_id'=>$txn->id])->delete();

                //add new using existing
                $txn_id = $this->txnModel
                    ->insert([
                        'month'=>$txn->month,
                        'year'=>$txn->year,
                        'block_id' => $txn->block_id,
                        'agency_type_id' => $txn->agency_type_id,
                        'fund_agency_id' => $txn->fund_agency_id,
                        'district_id' => $txn->district_id,
                        'user_id' => $txn->user_id,
                        'status' => (int)in_array($this->user->agency_type_id,$this->settings->auto_approve_users),
                    ]);

                $txnAmts = [];
                foreach ($this->request->getPost('misc') as $key => $item) {
                    $txnAmts[] = [
                        'txn_id' => $txn_id,
                        'head_id' => $key,
                        'amount' => $item
                    ];
                }
                $txnAmtModel = new MisctxnamtModel();
                $txnAmtModel->insertBatch($txnAmts);
                $json_data = [
                    'status' => true,
                ];
                $this->session->setFlashData('message','Other receipt updated.');

            } else {
                //getform
                $json_data = [
                    'status' => true,
                    'title' => 'Other receipt for ' . getMonthById($txn->month)['name'] . ' ' . getYear($txn->year),
                    'html' => $this->getForm($id)
                ];
            }
        }

        return $this->response->setJSON($json_data);
    }

    public function getForm($txn_id=null){

        helper('form');
        $txnModel = new MisctransactionModel();

        $agency_type_id = $this->user->agency_type_id;
        if($this->request->getGet('agency_type_id')){
            $agency_type_id = $this->request->getGet('agency_type_id');
        }

        if($txn_id){
            $txn = $this->txnModel->find($txn_id);
            $agency_type_id = $txn->agency_type_id;
        }

        $heads = $txnModel->getHeads($agency_type_id);

        $data['agency_types'] = [];
        $amts = [];

        $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        if($this->user->agency_type_id==$this->settings->block_user) {
            $data['agency_types'] = (new UserGroupModel())->getBlockUsers();
            $block = (new BlockModel())->find($this->user->block_id);
            $data['fund_agency_id'] = $block->fund_agency_id;
        }

        $data['agency_type_id'] = $agency_type_id;
        if ($txn_id) {
            $txnAmtModel = new MisctxnamtModel();
            $_amts = $txnAmtModel->where(['txn_id' => $txn_id])->asArray()->findAll();
            foreach ($_amts as $amt) {
                $amts[$amt['head_id']] = $amt['amount'];
            }
            $txn = $txnModel->find($txn_id);
            $data['agency_type_id'] = $txn->agency_type_id;
            $data['fund_agency_id'] = $txn->fund_agency_id;
        } else {

            $month = $this->request->getGet('month');
            $year = getCurrentYearId();
            if($this->request->getGet('year')){
                $year = $this->request->getGet('year');
            }

            if($this->user->agency_type_id==$this->settings->district_user) {
                //5 need to be set by config setting dynamically

                $filter = [
                    'year' => $year,
                    'month' => $month,
                    'district_id' => $this->user->district_id,
                    'fund_agency_id' => $data['fund_agency_id'],
                    'head_id' => 4
                ];

                $head_data = $txnModel->getHeadTotal($filter);

                $amts[5] = $head_data ? $head_data->total:'';

                //7 need to set by new module (return to ATMA)
                $amts[7] = $txnModel->getTotalRefund($filter);

            }

            $data['agency_type_id'] = $agency_type_id;
        }

        $data['heads'] = [];
        foreach ($heads as $head) {
            $data['heads'][] = [
                'id' => $head->id,
                'name' => $head->name,
                'value' => isset($amts[$head->id]) ? $amts[$head->id] : '',
            ];
        }
        $data['can_edit'] = (isset($txn) && $txn->status!=1);

        return view('\Admin\Transaction\Views\other_receipt_form', $data);
    }

    private function _validate($condition){
        $this->error="";
        $condition = [
            'month' => $condition['month'],
            'year' => $condition['year'],
            'agency_type_id' => $condition['agency_type_id'],
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'fund_agency_id' => $condition['fund_agency_id'],
            'user_id' => $this->user->user_id
        ];

        $txn = $this->txnModel
            ->where($condition)
            ->find();

        $cb = $this->cbModel
            ->where($condition)
            ->first();

        //check if cb is not approved
        if($cb && $cb->status==1){
            $this->error="Can not add other Receipt. Closing balance already exist" ;
        }else if($txn){
           $this->error="Can not add other Receipt. Other receipt already exist" ;
        }
        else if(env('soe.uploadDateValidation')){

            $upload_model = new AllowuploadModel();

            $ufilter = [
                'user_id' => $this->user->user_id
            ];

            $upload = $upload_model->getByDate($ufilter);

            $months = [];
            foreach ($upload as $item) {
                $months[] = $item['month'];
            }

            if (!in_array($condition['month'],$months)) {
                $this->error='Cannot add other receipt. Upload for the month is closed.';
            }
        }

        return $this->error;
    }

    
}
