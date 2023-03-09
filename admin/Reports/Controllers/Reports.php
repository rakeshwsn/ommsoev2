<?php 
namespace Admin\Reports\Controllers;

use Admin\Common\Models\CommonModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Models\ReportsModel;
use App\Controllers\AdminController;
use App\Traits\ReportTrait;

class Reports extends AdminController
{
    use ReportTrait;

    public function index() {
        $data = [];
        helper(['form']);
        return $this->template->view('Admin\Reports\Views\index', $data);
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

    public function pendingStatus($modulecode = 'expense')
    {
        $reportsModel = new ReportsModel();

        $filter['year_id'] = getCurrentYearId();
        if ($this->request->getGet('year_id')) {
            $filter['year_id'] = $this->request->getGet('year_id');
        }
        $filter['month_id'] = getCurrentMonthId();
        if ($this->request->getGet('month_id')) {
            $filter['month_id'] = $this->request->getGet('month_id');
        }
        $filter['district_id'] = null;
        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }
        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }
        $filter['phase'] = [0, 1, 2];
        $filter['module'] = 'expense';

        if ($modulecode == 'expense') {
            $data['blocks'] = $reportsModel->getPendingExpenses($filter);
        }

        $data['module'] = $filter['module'];
        $data['modules'] = [
            ['module' => 'Expense', 'modulecode' => 'expense'],
            ['module' => 'Fund Receipt', 'modulecode' => 'fund_receipt'],
        ];

        $data['year_id'] = $filter['year_id'];
        $data['month_id'] = $filter['month_id'];
        $data['districts'] = (new DistrictModel())->asArray()->findAll();
        $data['district_id'] = $filter['district_id'];

        return $this->template->view('Admin\Reports\Views\pending_status', $data);
    }

}
