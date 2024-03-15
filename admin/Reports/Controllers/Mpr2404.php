<?php 
namespace Admin\Reports\Controllers;

use App\Controllers\AdminController;
use App\Traits\ReportTrait;
use App\Traits\TreeTrait;
use Config\Url;

class Mpr extends AdminController
{
    use TreeTrait, ReportTrait {
        ReportTrait::generateTable insteadof TreeTrait;
        ReportTrait::getTable insteadof TreeTrait;
    }

    public function index($action = '') {
        $this->validateAction($action);

        $data = $this->initializeData();

        $data = $this->populateData($data);

        $data['components'] = $this->getComponents($data);

        $data = $this->addAdditionalData($data);

        return $this->renderView('Admin\Reports\Views\mpr_block', $data);
    }

    private function validateAction($action) {
        if (!in_array($action, ['', 'download'])) {
            throw new \InvalidArgumentException('Invalid action provided');
        }
    }

    private function initializeData() {
        return [
            'year_id' => getCurrentYearId(),
            'month_id' => getCurrentMonthId(),
            'agency_type_id' => '',
            'fund_agency_id' => $this->user->fund_agency_id ?? 1,
            'district_id' => '',
            'block_id' => $this->user->agency_type_id == $this->settings->block_user ? $this->user->block_id : '',
            'block_user_id' => '',
            'district_user_id' => '',
            'agency_types' => [],
            'districts' => [],
            'blocks' => [],
            'fund_agencies' => [],
            'components' => [],
            'month_name' => '',
            'fin_year' => '',
            'download_url' => Url::mprDownload . '?year=' . $data['year_id'] . '&month=' . $data['month_id'] . '&agency_type_id=' . $data['agency_type_id'],
        ];
    }

    private function populateData($data) {
        $data = $this->populateFromRequest($data);
        $data = $this->populateFromUser($data);

        return $data;
    }

    private function populateFromRequest($data) {
        if ($this->request->getGet('year')) {
            $data['year_id'] = $this->request->getGet('year');
        }

        if ($this->request->getGet('month')) {
            $data['month_id'] = $this->request->getGet('month');
        }

        if ($this->request->getGet('agency_type_id')) {
            $data['agency_type_id'] = $this->request->getGet('agency_type_id');
        }

        if ($this->request->getGet('fund_agency_id')) {
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }

        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }

        return $data;
    }

    private function populateFromUser($data) {
        if ($this->user->agency_type_id == $this->settings->block_user) {
            $data['block_id'] = $this->user->block_id;
        }

        if ($this->user->agency_type_id == $this->settings->district_user) {
            $data['district_id'] = $this->user->district_id;
        }

        if ($data['block_id']) {
            $where = ['block_id' => $data['block_id'], 'user_group_id' => $this->settings->block_user];
            $user = (new UserModel())->where($where)->first();
            $data['block_user_id'] = $user->id;
        }

        if ($data['district_id']) {
            $where = ['district_id' => $data['district_id'], 'user_group_id' => $this->settings->district_user];
            $user = (new UserModel())->where($where)->first();
            $data['district_user_id'] = $user->id;
        }

        return $data;
    }

    private function getComponents($data) {
        $reportModel = new ReportsModel();
        $filter = $this->getFilter($data);

        $components = $reportModel->getMpr($filter);

        $components = $this->buildTree($components, 'parent', 'scomponent_id');

        $components = $this->getTable($components, 'view');

        return $components;
    }

    private function getFilter($data) {
        $filter = [
            'month_id' => $data['month_id'],
            'year_id' => $data['year_id'],
            'user_id' => $this->user->id,
        ];

        if ($data['agency_type_id']) {
            $filter['agency_type_id'] = $data['agency_type_id'];
        }

        if ($data['district_id']) {
            $filter['district_id'] = $data['district_id'];
            $filter['ag
