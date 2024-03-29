<?php 
namespace Admin\Transaction\Controllers;

use Admin\Common\Models\AllowuploadModel;
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
use Admin\Transaction\Models\TransactionComponentModel;
use Admin\Transaction\Models\TransactionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use Config\Url;

class Correction extends AdminController {

    use TreeTrait;

    public function index() {
        $block_model = new BlockModel();
        $this->template->add_package(['datatable','jquery_loading'],true);
        $data = [];
        $data['months'] = getMonths();
        $data['years'] = getAllYears();

        $data['month_id'] = getMonthIdByMonth(date('m'));
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

        $data['upload_statuses'] = (new TransactionModel())->getBlockUploadStatus($filter);
        foreach ($data['upload_statuses'] as &$upload_status) {
            $status = $upload_status->status;
            if($upload_status->status==0){
                $upload_status->status = '<label class="badge badge-warning">'.$this->statuses[$upload_status->status].'</label>';
            }
            if($upload_status->status==1){
                $upload_status->status = '<label class="badge badge-success">'.$this->statuses[$upload_status->status].'</label>';
            }
            if($upload_status->status==2){
                $upload_status->status = '<label class="badge badge-danger">'.$this->statuses[$upload_status->status].'</label>';
            }
            if($upload_status->status==3){
                $upload_status->status = '<label class="badge badge-info">'.$this->statuses[$upload_status->status].'</label>';
            }

            $upload_status->action = '';
            $url_params = '?txn_type='.$upload_status->transaction_type
                .'&txn_id='.$upload_status->txn_id
                .'&year='.$upload_status->year_id
                .'&month='.$upload_status->month_id
                .'&block_id='.$upload_status->block_id
                .'&agency_type_id='.$upload_status->agency_type_id
                .'&fund_agency_id='.$upload_status->fund_agency_id;
            $upload_status->action = '';
            if($status != 3) {
                if ($upload_status->transaction_type == 'fund_receipt' || $upload_status->transaction_type == 'expense') {
                    $upload_status->action = site_url(Url::correctionTransaction . $url_params);
                }
                if ($upload_status->transaction_type == 'other_receipt') {
                    $upload_status->action = site_url(Url::correctionOtherReceipt . $url_params);
                }
                if ($upload_status->transaction_type == 'closing_balance') {
                    $upload_status->action = site_url(Url::correctionClosingBalance . $url_params);
                }
                if ($upload_status->transaction_type == 'mis') {
                    $upload_status->action = site_url(Url::correctionMIS . $url_params);
                }
            }

            $upload_status->created_at = $upload_status->created_at ? ymdToDmy($upload_status->created_at):'-';
        }

        $data['modules'] = (new CommonModel())->getModules();

        return $this->template->view('Admin\Transaction\Views\correction', $data);
    }

    public function transaction() {
        $txnModel = new TransactionModel();
        $txnCompModel = new TransactionComponentModel();

        $data = [];

        $txn_id = $this->request->getGet('txn_id');

        if($this->request->getMethod(1)=='POST'){

            $txn = $txnModel->find($txn_id);

            $id = $txn->id;

            $txnModel->delete($id);

            $txn_data = [
                'block_id'=>$txn->block_id,
                'district_id'=>$txn->district_id,
                'agency_type_id'=>$txn->agency_type_id,
                'month' => $txn->month,
                'year' => $txn->year,
                'filename' => $txn->filename,
                'status' => $txn->status,
                'date_added' => date('Y-m-d'),
                'user_id' => $txn->user_id,
                'transaction_type' => $txn->transaction_type,
                'fund_agency_id' => $txn->fund_agency_id
            ];
            $txn_id = $txnModel->insert($txn_data);

            //delete the existing transaction components
            $txnCompModel->where(['transaction_id'=>$id])->delete();
            $components = [];

            foreach ($this->request->getPost() as $component_id => $value) {
                $components[] = [
                    'transaction_id' => $txn_id,
                    'component_id' => $component_id,
                    'physical' => $value['phy'],
                    'financial' => $value['fin']
                ];
            }
            $txnCompModel->insertBatch($components);

            $this->session->setFlashdata('message','Your changes have been saved');

            $url_params = $this->getUrlParam();

            return redirect()->to(Url::correction.$url_params);
        }

        $txn = new \stdClass();

        if ($txn_id && ($this->request->getMethod(true) != 'POST')) {
            $txn = $txnModel->find($txn_id);

            if(!$txn){
                $this->session->setFlashdata('message','Transaction not found!');
                $url_params = $this->getUrlParam();

                return redirect()->to(Url::correction.$url_params);
            }

        }

        if ($txn && $this->user->canUpload($txn->month, $txn->year)) {
            $action = 'edit';
        } else {
            $action = 'show';
        }

        // for button
        $data['show_form'] = true;

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

        $data['approval'] = false;

        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form',$form_data);

        return $this->template->view('Admin\Transaction\Views\edit', $data);
    }

    public function otherReceipt() {
        helper('form');
        $txnModel = new MisctransactionModel();
        $txnAmtModel = new MisctxnamtModel();

        $txn_id = $this->request->getGet('txn_id');
        $txn_type = $this->request->getGet('txn_type');

        $txn = $txnModel->find($txn_id);

        if($this->request->getMethod(1)=='POST'){
            $head_data = [];

            //delete existing
            $txnAmtModel->where('txn_id',$txn_id)->delete();

            //add new
            foreach ($this->request->getPost('head') as $head_id => $head) {
                $head_data[] = [
                    'txn_id' => $txn_id,
                    'head_id' => $head_id,
                    'amount' => $head,
                ];
            }

            if($head_data){
                $txnAmtModel->insertBatch($head_data);
            }

            $this->session->setFlashdata('message','Your changes have been saved.');
            $url_params = $this->getUrlParam();

            return redirect()->to(Url::correction.$url_params);
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

        $data['approval'] = false;

        $data['approve_form'] = '';

        $data['correction'] = true;

        return $this->template->view('\Admin\Transaction\Views\approve_other_receipt', $data);
    }

    public function closingBalance() {
        helper('form');
        $data = [];
        $cbModel = new ClosingbalanceModel();

        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $block_id = $this->request->getGet('block_id');
        $agency_type_id = $this->request->getGet('agency_type_id');

        $filter = [
            'block_id' => $block_id,
            'year' => $year,
            'agency_type_id' => $agency_type_id,
        ];

        $filter['month'] = $month;

        $cb = $cbModel->where($filter)->first();

        if($this->request->getMethod(1)=='POST'){

            $cbModel->delete($cb->id);

            //insert
            $data = [
                'user_id' => $cb->user_id,
                'month' => $cb->month,
                'year' => $cb->year,
                'block_id' =>$cb->block_id,
                'district_id' => $cb->district_id,
                'agency_type_id' => $cb->agency_type_id,
                'fund_agency_id' => $cb->fund_agency_id,
                'status' => $cb->status
            ];

            $data['advance'] = (float)$this->request->getPost('advance');
            $data['advance_file'] = $cb->advance_file;
            $data['bank'] = (float)$this->request->getPost('bank');
            $data['bank_file'] = $cb->bank_file;
            $data['cash'] = (float)$this->request->getPost('cash');
            $data['cash_file'] = $cb->cash_file;

            $cbModel->save($data);

            $this->session->setFlashdata('message','Your changes have been saved.');
            $url_params = $this->getUrlParam();

            return redirect()->to(Url::correction.$url_params);
        }

        $filter['user_id'] = $cb->user_id;
        $filter['fund_agency_id'] = (new BlockModel())->find($cb->block_id)->fund_agency_id;

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

        $data['approval'] = false;

        $data['approve_form'] = '';

        $data['correction'] = true;

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

            $id = $txn->id;

            $misDetailModel->where(['submission_id' => $id])->delete();

            $achievements = [];

            foreach ($this->request->getPost('achievement') as $indicator_id => $value) {
                $achievements[] = [
                    'submission_id' => $id,
                    'output_indicator_id' => $indicator_id,
                    'achievement' => isset($value['number']) ? $value['number'] : '',
                    'file' => isset($value['file']) ? $value['file'] : ''
                ];
            }

            $misDetailModel->insertBatch($achievements);

            $this->session->setFlashdata('message', 'MIS Updated Successfully');

            $url_params = $this->getUrlParam();

            return redirect()->to(Url::correction.$url_params);
        }
        $txn = new \stdClass();
        if ($txn_id && ($this->request->getMethod(true) != 'POST')) {
            $txn = $misModel->find($txn_id);
        }

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
            'user_group' => $agency_type_id == 5 ? [5, 6] : $agency_type_id,
            'component_category' => 'program',
            'fund_agency_id' => $fund_agency_id
        ];

        $filter['component_agency_type_id'] = [5, 6, 7, 0];
        $filter['component_category'] = ['program'];

        if($district_id){
            $filter['district_id'] = $district_id;
        }

        $components = $compModel->getComponents($filter);

        $components = $this->buildTree($components, 'parent', 'assign_id');

        $misController = new MIS();

        $action = 'edit';
        $data['show_form'] = true;

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

        $data['approval'] = false;
        $form_data = $this->getForm();
        $data['approve_form'] = view('\Admin\Transaction\Views\approve_form',$form_data);

        return $this->template->view('Admin\Transaction\Views\correction_mis', $data);
    }

    public function getForm() {

        $txn_type = $this->request->getGet('txn_type');
        $txn_id = $this->request->getGet('txn_id');

        $data['statuses'] = [
            [
                'id' => 0,
                'name' => 'Not Approved',
            ],
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

    private function getUrlParam()
    {
        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');
        $agency_type_id = $this->request->getGet('agency_type_id');
        $txn_type = $this->request->getGet('txn_type');
        $fund_agency_id = $this->request->getGet('fund_agency_id');
//        $block_id = $this->request->getGet('block_id');
        $block_id = '';
        return '?year=' . $year
            . '&month=' . $month
            . '&year=' . $year
            . '&agency_type_id=' . $agency_type_id
            . '&txn_type=' . $txn_type
            . '&fund_agency_id=' . $fund_agency_id
            . '&block_id=' . $block_id;

    }

}
