<?php 
namespace Admin\Transaction\Controllers;

use Admin\Common\Models\CommonModel;
use Admin\Component\Models\ComponentModel;
use Admin\Components\Models\ComponentsModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\MIS\Controllers\MIS;
use Admin\MIS\Models\MISDetailModel;
use Admin\MIS\Models\MISModel;
use Admin\Transaction\Models\ClosingbalanceModel;
use Admin\Transaction\Models\MisctransactionModel;
use Admin\Transaction\Models\MisctxnamtModel;
use Admin\Transaction\Models\TransactionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use Config\Url;

class Approve extends AdminController {

    use TreeTrait;

    public function index() {

        $block_model = new BlockModel();
        $this->template->add_package(['datatable','jquery_loading'],true);
        $data = [];
        $data['months'] = getMonths();
        $data['years'] = getAllYears();

        $data['month_id'] = getCurrentMonthId();
        $data['year_id'] = getCurrentYearId();

        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }
        $data['txn_type'] = '';
        if($this->request->getGet('txn_type')){
            $data['txn_type'] = $this->request->getGet('txn_type');
        }

        $data['agency_type_id'] = '';
        if($this->request->getGet('agency_type_id')) {
            $data['agency_type_id'] = $this->request->getGet('agency_type_id');
        }

        $data['district_id'] = $this->user->district_id;
        if($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }

        $data['fund_agency_id'] = $this->user->fund_agency_id;
        if($this->request->getGet('fund_agency_id')) {
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        $data['block_id'] = '';
        if($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }

        $data['message'] = $this->session->getFlashdata('message');

        $data['blocks'] = [];
        if($this->user->agency_type_id!=$this->settings->block_user) {
            $data['blocks'] = $block_model->where(['district_id' => $this->user->district_id])
                ->where(['fund_agency_id' => $data['fund_agency_id']])->asArray()->findAll();
        }

        $data['districts'] = [];
        if($this->user->agency_type_id!=$this->settings->district_user
            && $this->user->agency_type_id!=$this->settings->block_user) {
            $data['districts'] = (new DistrictModel)->asArray()->findAll();
        }

        $data['agency_types'] = [];
        foreach ($this->settings->user_can_access as $user_group => $user_can_access_grp) {
            if($this->user->agency_type_id==$user_group){
                $data['agency_types'] = (new UserGroupModel)->whereIn('id',
                    $user_can_access_grp)->orderBy('name')->asArray()->findAll();
            }
        }

        $data['fund_agencies'] = [];
        if($this->user->agency_type_id!=$this->settings->district_user){
            $data['fund_agencies'] = $block_model->getFundAgencies();
        }

        $filter = [
            'month_id' => $data['month_id'],
            'year_id' => $data['year_id'],
            'agency_type_id' => $data['agency_type_id'],
            'transaction_type' => $data['txn_type'],
            'fund_agency_id' => $data['fund_agency_id'],
            'block_id' => $data['block_id'],
            'district_id' => $data['district_id'],
            'user_id' => $this->user->user_id,
        ];

        $data['upload_statuses'] = (new TransactionModel())->getTransactionStatus($filter);

        $data['modules'] = (new CommonModel())->getModules();

        foreach ($data['upload_statuses'] as &$status) {
            if($status->status==0){
                $status->status = '<label class="badge badge-warning">'.$this->statuses[$status->status].'</label>';
            }
            if($status->status==1){
                $status->status = '<label class="badge badge-success">'.$this->statuses[$status->status].'</label>';
            }
            if($status->status==2){
                $status->status = '<label class="badge badge-danger">'.$this->statuses[$status->status].'</label>';
            }
            if($status->status==3){
                $status->status = '<label class="badge badge-info">'.$this->statuses[$status->status].'</label>';
            }

            $status->action = '';
            $url_params = '?txn_type='.$status->transaction_type
                .'&txn_id='.$status->txn_id
                .'&year='.$status->year_id
                .'&month='.$status->month_id
                .'&block_id='.$status->block_id
                .'&agency_type_id='.$status->agency_type_id
                .'&fund_agency_id='.$status->fund_agency_id;
            if($status->transaction_type=='fund_receipt' || $status->transaction_type=='expense'){
                $status->action = site_url(Url::approveTransaction.$url_params);
            }
            if($status->transaction_type=='other_receipt'){
                $status->action = site_url(Url::approveOtherReceipt.$url_params);
            }
            if($status->transaction_type=='closing_balance'){
                $status->action = site_url(Url::approveClosingBalance.$url_params);
            }
            if($status->transaction_type=='mis'){
                $status->action = site_url(Url::approveMIS.$url_params);
            }

            $status->created_at = $status->created_at ? ymdToDmy($status->created_at):'-';
        }

        return $this->template->view('Admin\Transaction\Views\approve', $data);
    }

    public function transaction() {
        $txnModel = new TransactionModel();

        $data = [];

        $txn_id = $this->request->getGet('txn_id');

        if($this->request->getMethod(1)=='POST'){

            $txn = $txnModel->find($txn_id);

            $txn_data = [
                'status' => $this->request->getPost('status'),
                'status_user' => $this->user->user_id,
                'remarks' => $this->request->getPost('remarks')
            ];
            $txnModel->update($txn->id,$txn_data);

            $this->session->setFlashdata('message','Your changes have been saved.');
            return redirect()->to(Url::approve);
        }

        $txn = new \stdClass();

        if ($txn_id && ($this->request->getMethod(true) != 'POST')) {
            $txn = $txnModel->find($txn_id);

            if(!$txn){
                $this->session->setFlashdata('message','Transaction not found!');
                $url_params = $this->getUrlParam();

                return redirect()->to(Url::approve.$url_params);
            }

        }

        $action = 'view';
        $data['show_form'] = false;

        $data['block'] = $txn->block;
        $data['district'] = $txn->district;
        $data['agency_type'] = (new UserGroupModel())->find($txn->agency_type_id)->name;
        $data['fund_agency'] = $txn->fund_agency_id ? (new CommonModel())->getFundAgency($txn->fund_agency_id)['name']:'-';
        $data['month'] = getMonthById($txn->month)['name'];
        $data['year'] = getYear($txn->year);
        $data['date_added'] = ymdToDmy($txn->date_added);
        $data['phy'] = $txn->physical;
        $data['fin'] = $txn->financial;

        $data['status'] = $this->statuses[$txn->status];

        $data['remarks'] = $txn->remarks;

        $data['txn_type_text'] = $txn->transaction_type=='expense'?'Expense':'Fund Receipt';

        $filter = [
            'user_id' => $txn->user_id,
            'block_id' => $txn->block_id,
            'agency_type_id' => $txn->agency_type_id,
            'district_id' => $txn->district_id,
            'month' => $txn->month,
            'year' => $txn->year,
        ];

        $user = (new UserModel())->find($txn->user_id);
        $filter['fund_agency_id'] = $user->fund_agency_id;

        if ($txn->agency_type_id == $this->settings->block_user || $txn->agency_type_id == $this->settings->cbo_user) {
            $filter['component_agency_type_id'] = 5; //fa/cbo --to be added to settings
            $filter['category'] = 'program';
        }

        $block_components = $txnModel->getBlockDistrictReport($filter);

        $components = $this->buildTree($block_components, 'parent', 'scomponent_id');

        $data['components'] = $this->getTable($components,$txn->transaction_type,$action);

        $data['approval'] = $txn->status!=1;

        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form',$form_data);

        return $this->template->view('Admin\Transaction\Views\approve_transaction', $data);
    }

    private function getUrlParam()
    {
        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $agency_type_id = $this->request->getGet('agency_type_id');
        $txn_type = $this->request->getGet('txn_type');
        $fund_agency_id = $this->request->getGet('fund_agency_id');
        $block_id = $this->request->getGet('block_id');
        return '?year=' . $year
            . '&month=' . $month
            . '&year=' . $year
            . '&agency_type_id=' . $agency_type_id
            . '&txn_type=' . $txn_type
            . '&fund_agency_id=' . $fund_agency_id
            . '&block_id=' . $block_id;

    }

    public function getForm()
    {
        $txn_type = $this->request->getGet('txn_type');
        $txn_id = $this->request->getGet('txn_id');

        $data['statuses'] = [
            [
                'id' => 1,
                'name' => 'Approved',
            ],
            [
                'id' => 2,
                'name' => 'Rejected',
            ],
        ];
        $title = 'Approve';
        if ($txn_type == 'expense' || $txn_type == 'fund_receipt') {
            $txn = (new TransactionModel())->find($txn_id);
            $title .= $txn->transaction_type == 'expense' ? ' Expense' : ' Fund receipt';
        }
        if ($txn_type == 'other_receipt') {
            $txn = (new MisctransactionModel())->find($txn_id);
            $title .= ' Other Receipt';
        }
        if ($txn_type == 'closing_balance') {
            $txn = (new ClosingbalanceModel())->find($txn_id);
            $title .= ' Closing Balance';
        }
        if ($txn_type == 'mis') {
            $txn = (new MISModel())->find($txn_id);
            $title .= ' MIS';
        }

        $data['status_id'] = $txn->status;
        $data['remarks'] = $txn->remarks;

        $data['title'] = $title;

        return $data;
    }

    public function otherReceipt() {
        helper('form');
        $txnModel = new MisctransactionModel();

        $txn_id = $this->request->getGet('txn_id');
        $txn_type = $this->request->getGet('txn_type');

        $txn = $txnModel->find($txn_id);

        if($this->request->getMethod(1)=='POST'){
            $txn_data = [
                'status' => $this->request->getPost('status'),
                'status_user' => $this->user->user_id,
                'remarks' => $this->request->getPost('remarks')
            ];
            $txnModel->update($txn->id,$txn_data);

            $this->session->setFlashdata('message','Your changes have been saved.');
            $url_params = $this->getUrlParam();

            return redirect()->to(Url::approve.$url_params);
        }

        $heads = $txnModel->getHeads($txn->agency_type_id);
        $data['agency_types'] = [];
        $amts = [];

        $data['agency_type_id'] = $txn->agency_type_id;
        if ($txn_id) {
            $txnAmtModel = new MisctxnamtModel();
            $_amts = $txnAmtModel->where(['txn_id' => $txn_id])->asArray()->findAll();

            foreach ($_amts as $amt) {
                $amts[$amt['head_id']] = $amt['amount'];
            }
            $txn = $txnModel->find($txn_id);
            $data['agency_type_id'] = $txn->agency_type_id;
            $data['fund_agency_id'] = $txn->fund_agency_id;
        }

        $data['heads'] = [];

        foreach ($heads as $head) {
            $data['heads'][] = [
                'id' => $head->id,
                'name' => $head->name,
                'value' => isset($amts[$head->id]) ? $amts[$head->id] : '',
            ];
        }

        $data['block'] = (new BlockModel())->find($txn->block_id)->name;
        $data['district'] = (new DistrictModel())->find($txn->district_id)->name;
        $data['agency_type'] = (new UserGroupModel())->find($txn->agency_type_id)->name;
        $data['fund_agency'] = $txn->fund_agency_id ? (new CommonModel())->getFundAgency($txn->fund_agency_id)['name']:'-';
        $data['month'] = getMonthById($txn->month)['name'];
        $data['year'] = getYear($txn->year);
        $data['date_added'] = ymdToDmy($txn->created_at);

        $data['status'] = $this->statuses[$txn->status];

        $data['remarks'] = $txn->remarks;

        $data['txn_type_text'] = 'Other Receipt';

        $data['approval'] = $txn->status!=1;

        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form',$form_data);

        return $this->template->view('\Admin\Transaction\Views\approve_other_receipt', $data);
    }

    public function closingBalance() {
        helper('form');
        $data = [];
        $cbModel = new ClosingbalanceModel();

        $id = $this->request->getGet('txn_id');

        $cb = $cbModel->find($id);

        $month = $cb->month;

        if($this->request->getMethod(1)=='POST'){

            //insert
            $data = [
                'status' => (int)$this->request->getPost('status'),
                'status_user' => (int)$this->user->user_id,
                'remarks' => $this->request->getPost('remarks'),
            ];

            $cbModel->update($cb->id,$data);

            $this->session->setFlashdata('message','Your changes have been saved.');
            $url_params = $this->getUrlParam();

            return redirect()->to(Url::approve.$url_params);
        }

        $filter = ['user_id' => $cb->user_id];
        $filter['year'] = $cb->year;
        $filter['month'] = $cb->month;
        $filter['agency_type_id'] = $cb->agency_type_id;
        $filter['block_id'] = $cb->block_id;
        $filter['district_id'] = $cb->district_id;
        if($cb->block_id){
            $filter['fund_agency_id'] = (new BlockModel())->find($cb->block_id)->fund_agency_id;
        } else if($cb->district_id) {
            $filter['fund_agency_id'] = $cb->fund_agency_id;
        }

        $ledger =  $cbModel->getLedgerReport($filter,'array');

        $key = array_search($month,array_column($ledger,'month_id'));

        $data['summary'] = $ledger[$key];

        foreach($cbModel->getFieldNames() as $field){
            if($cb){
                $data[$field] = $cb->{$field};
            } else {
                $data[$field] = '';
            }
        }

        $data['status'] = $this->statuses[$data['status']];

        $data['advance_file_url'] = '';
        if($cb && $cb->advance_file){
            $data['advance_file_url'] = anchor(base_url('uploads/cb/'.$cb->advance_file),$cb->advance_file,'target="_blank"');
        }
        $data['bank_file_url'] = '';
        if($cb && $cb->bank_file){
            $data['bank_file_url'] = anchor(base_url('uploads/cb/'.$cb->bank_file),$cb->bank_file,'target="_blank"');
        }
        $data['cash_file_url'] = '';
        if($cb && $cb->cash_file){
            $data['cash_file_url'] = anchor(base_url('uploads/cb/'.$cb->cash_file),$cb->cash_file,'target="_blank"');
        }

        $data['approval'] = $cb->status!=1;

        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form',$form_data);

        return $this->template->view('\Admin\Transaction\Views\approve_closing_balance', $data);
    }

    public function mis() {
        helper('form');
        $data = [];
        $misModel = new MISModel();
        $compModel = new ComponentsModel();
        $misDetailModel = new MISDetailModel();
        $this->template->add_package(['uploader','jquery_loading'],true);

        $txn_id = $this->request->getGet('txn_id');

        if($this->request->getMethod(1)=='POST'){
            $txn = $misModel->find($txn_id);

            $txn_data = [
                'status' => (int)$this->request->getPost('status'),
                'status_user' => $this->user->user_id,
                'remarks' => $this->request->getPost('remarks')
            ];
            $misModel->update($txn->id,$txn_data);

            $this->session->setFlashdata('message','Your changes have been saved.');

            $url_params = $this->getUrlParam();

            return redirect()->to(Url::approve.$url_params);
        }
        $txn = new \stdClass();
        if ($txn_id && ($this->request->getMethod(true) != 'POST')) {
            $txn = $misModel->find($txn_id);
        }
        $action = 'view';
        $data['show_form'] = false;
        $block_id = $txn->block_id;
        $district_id = $txn->district_id;
        $agency_type_id = $txn->agency_type_id;
        $fund_agency_id = $txn->fund_agency_id;
        $month = $txn->month;
        $year = $txn->year;
        $misdetails = $misDetailModel->asArray()->where('submission_id', $txn_id)->findAll();

        $filter = [
            'block_id' => $block_id,
            'month' => $month,
            'year' => $year,
            'user_group' => $agency_type_id,
        ];
//        $filter['component_agency_type_id'] = [5, 6, 7, 0];
        $filter['component_category'] = ['program'];
        $filter['fund_agency_id'] = $fund_agency_id;

        if($district_id){
            $filter['district_id'] = $district_id;
        }

        $components = $compModel->getComponents($filter);

        $components = $this->buildTree($components, 'parent', 'assign_id');

        $misController = new MIS();

        $data['components'] = $misController->getTable($components,$misdetails,$action);

        $data['block'] = $block_id ? (new BlockModel)->find($block_id)->name:'-';
        $data['district'] = $district_id ? (new DistrictModel)->find($district_id)->name:'-';
        $data['agency_type'] = '-';
        if($agency_type_id){
            $data['agency_type'] = (new UserGroupModel)->find($agency_type_id)->name;
            if($data['agency_type']=='FA'){
                $data['agency_type'] = 'FA/CBO';
            }
        }

        $data['month'] = getMonthById($month)['name'];
        $data['year'] = getYear($year);
        $data['date_added'] = date('Y/m/d');
        $data['status'] = $this->statuses[$txn->status];
        $data['upload_url'] = Url::MISUpload;

        $data['approval'] = $txn->status!=1;

        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form',$form_data);

        return $this->template->view('Admin\Transaction\Views\approve_mis', $data);
    }

}
