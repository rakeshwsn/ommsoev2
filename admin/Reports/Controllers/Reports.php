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

    public function __construct()
    {
        $this->reportModel = new ReportsModel();
    }

    public function index()
    {
        $data = [];
        helper(['form']);
        return $this->template->view('Admin\Reports\Views\index', $data);
    }

    public function uploadStatus()
    {
        $data = [
            'districts' => (new DistrictModel())->asArray()->findAll(),
            'district_id' => $this->request->getGet('district_id', ''),
        ];

        $data = array_merge($this->getUploadStatus($data), $data);

        return $this->template->view('Admin\Reports\Views\upload_status', $data);
    }

    public function misStatus($filter)
    {
        $statusdata = $this->reportModel->getMisStatus($filter);
        return $statusdata ? $statusdata->status : '';
    }

    public function frStatus($filter)
    {
        $statusdata = $this->reportModel->getFRStatus($filter);
        return $statusdata ? $statusdata->status : '';
    }

    public function pendingStatus()
    {
        $reportsModel = new ReportsModel();

        $filter = [
            'year_id' => getCurrentYearId(),
            'month_id' => 0,
            'district_id' => null,
            'fund_agency_id' => $this->user->fund_agency_id,
        ];

        if ($this->request->getGet('year_id')) {
            $filter['year_id'] = $this->request->getGet('year_id');
        }

        if ($this->request->getGet('month_id')) {
            $filter['month_id'] = $this->request->getGet('month_id');
        }

        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }

        if ($this->request->getGet('fund_agency_id')) {
            $filter['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        $filter['phase'] = [0, 1, 2];

        $data = [
            'blocks' => $reportsModel->getPendingStatuses($filter),
            'statuses' => $this->statuses,
            'year_id' => $filter['year_id'],
            'month_id' => $filter['month_id'],
            'districts' => $this->getDistricts(),
            'district_id' => $filter['district_id'],
        ];

        foreach ($data['blocks'] as &$block) {
            $block['action'] = $this->getActionUrl($block);
            $block['status'] = $this->getStatusLabel($block['status']);
        }

        return $this->template->view('Admin\Reports\Views\pending_status', $data);
    }

    private function getDistricts()
    {
        if ($this->user->district_id) {
            return (new DistrictModel())->asArray()->where('id', $this->user->district_id)->find();
        }

        return (new DistrictModel())->asArray()->findAll();
    }

    private function getActionUrl($block)
    {
        $url_params = '?txn_type=' . $block['transaction_type'] . '&txn_id=' . $block['transaction_id'];

        switch ($block['transaction_type']) {
            case 'fund_receipt':
            case 'expense':
                return site_url(Url::approveClosingBalance . $url_params);
            case 'other_receipt':
                return site_url(Url::approveOtherReceipt . $url_params);
            case 'closing_balance':
                return site_url(Url::approveClosingBalance . $url_params);
            case 'mis':
                return site_url(Url::approveMIS . $url_params);
            default:
                return '';
        }
    }

    private function getStatusLabel($status)
    {
        switch ($status) {
            case 0:
                return '<label class="badge badge-warning">' . $this->statuses[$status] . '</label>';
            case 1:
                return '<label class="badge badge-success">' . $this->statuses[$status] . '</label>';
            case 2:
                return '<label class="badge badge-danger">' . $this->statuses[$status] . '</label>';
            case 3:
                return '<label class="badge badge-info">' . $this->statuses[$status] . '</label>';
            default:
                return '';
        }
    }
}
