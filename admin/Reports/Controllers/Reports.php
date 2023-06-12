<?php 
namespace Admin\Reports\Controllers;

use Admin\Common\Models\CommonModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Models\ReportsModel;
use App\Controllers\AdminController;
use App\Traits\ReportTrait;
use Config\Url;

class Reports extends AdminController
{
    use ReportTrait;

    private $reportModel;

    public function __construct(){
		$this->reportModel=new ReportsModel();
    }
    public function index() {
        $data = [];
        helper(['form']);
        return $this->template->view('Admin\Reports\Views\index', $data);
    }

    public function uploadStatus_old() {

        $data['districts'] = (new DistrictModel())->asArray()->findAll();
        $data['district_id'] = '';
        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        }
        $data = array_merge($this->getUploadStatus($data),$data);

        return $this->template->view('Admin\Reports\Views\upload_status',$data);
    }

    public function uploadStatus() {

        $data['districts'] = (new DistrictModel())->asArray()->findAll();
        $data['district_id'] = '';
        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        }
        $data = array_merge($this->getUploadStatus($data),$data);

        return $this->template->view('Admin\Reports\Views\upload_status',$data);

    }

    public function misStatus($filter){
        //print_r($filter);
        $statusdata=$this->reportModel->getMisStatus($filter);
        if($statusdata){
            return $statusdata->status;
        }
    }

    public function frStatus($filter){
        $statusdata=$this->reportModel->getFRStatus($filter);
        if($statusdata){
            return $statusdata->status;
        }
    }

    public function pendingStatus()
    {
        $reportsModel = new ReportsModel();

        $filter['year_id'] = getCurrentYearId();
        if ($this->request->getGet('year_id')) {
            $filter['year_id'] = $this->request->getGet('year_id');
        }
        $filter['month_id'] = 0;
        if ($this->request->getGet('month_id')) {
            $filter['month_id'] = $this->request->getGet('month_id');
        }
        $filter['district_id'] = null;
        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }
        $filter['fund_agency_id'] = $this->user->fund_agency_id;
        if ($this->request->getGet('fund_agency_id')) {
            $filter['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }
        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        $filter['phase'] = [0, 1, 2];

        $data['blocks'] = $reportsModel->getPendingStatuses($filter);

        foreach ($data['blocks'] as &$block) {
            if($block['transaction_type']=='expense' || $block['transaction_type']=='fund_receipt'){
                $block['action'] = site_url(Url::approveClosingBalance);

            }
            if($block['status']==0){
                $block['status']= '<label class="badge badge-warning">'.$this->statuses[$block['status']].'</label>';
            }
            if($block['status']==1){
                $block['status']= '<label class="badge badge-success">'.$this->statuses[$block['status']].'</label>';
            }
            if($block['status']==2){
                $block['status']= '<label class="badge badge-danger">'.$this->statuses[$block['status']].'</label>';
            }
            if($block['status']==3){
                $block['status']= '<label class="badge badge-info">'.$this->statuses[$block['status']].'</label>';
            }

            $block['action'] = '';
            $url_params = '?txn_type='.$block['transaction_type']
                .'&txn_id='.$block['transaction_id'];
            if($block['transaction_type'] =='fund_receipt' || $block['transaction_type'] =='expense'){
                $block['action'] = site_url(Url::approveTransaction.$url_params);
            }
            if($block['transaction_type'] =='other_receipt'){
                $block['action'] = site_url(Url::approveOtherReceipt.$url_params);
            }
            if($block['transaction_type'] =='closing_balance'){
                $block['action'] = site_url(Url::approveClosingBalance.$url_params);
            }
            if($block['transaction_type'] =='mis'){
                $block['action'] = site_url(Url::approveMIS.$url_params);
            }
        }

        $data['statuses'] = $this->statuses;

        $data['year_id'] = $filter['year_id'];
        $data['month_id'] = $filter['month_id'];
        if ($this->user->district_id) {
            $data['districts'] = (new DistrictModel())->asArray()
                ->where('id', $this->user->district_id)->find();
        } else {
            $data['districts'] = (new DistrictModel())->asArray()->findAll();
        }
        $data['district_id'] = $filter['district_id'];

        return $this->template->view('Admin\Reports\Views\pending_status', $data);
    }

}
