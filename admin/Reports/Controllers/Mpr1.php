<?php

declare(strict_types=1);

namespace Admin\Reports\Controllers;

use Admin\Block\Models\BlockModel;
use Admin\Common\Models\CommonModel;
use Admin\Reports\Models\ReportsModel;
use Admin\Transaction\Models\TransactionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use App\Traits\ReportTrait;

class Mpr extends AdminController
{
    use ReportTrait;

    public function index(string $action = ''): string
    {
        $data = $this->setDefaultValues();

        $this->setYearId($data);
        $this->setMonthId($data);
        $this->setAgencyTypeId($data);
        $this->setDistrictId($data);
        $this->setBlockId($data);

        $reportModel = new ReportsModel();
        $components = $this->getMprComponents($reportModel, $data);
        $data['components'] = $this->buildTree($components, 'parent', 'component_id');
        $data['components'] = $this->getTable($data['components'], 'view');

        $this->setDownloadUrl($data);

        return $this->template->view('Admin\Reports\Views\mpr_block', $data);
    }

    private function setDefaultValues(): array
    {
        return [
            'year_id' => getCurrentYearId(),
            'month_id' => getMonthIdByMonth(date('m')),
            'agency_type_id' => '',
            'district_id' => '',
            'block_id' => '',
            'agency_types' => [],
            'districts' => [],
            'blocks' => [],
            'fund_agencies' => [],
            'years' => getAllYears(),
            'months' => getAllMonths(),
            'filter_panel' => '',
            'download_url' => '',
        ];
    }

    private function setYearId(array &$data): void
    {
        if ($this->request->getGet('year')) {
            $data['year_id'] = (int) $this->request->getGet('year');
        }
    }

    private function setMonthId(array &$data): void
    {
        if ($this->request->getGet('month')) {
            $data['month_id'] = (int) $this->request->getGet('month');
        }
    }

    private function setAgencyTypeId(array &$data): void
    {
        if ($this->request->getGet('agency_type_id')) {
            $data['agency_type_id'] = (int) $this->request->getGet('agency_type_id');
        }
    }

    private function setDistrictId(array &$data): void
    {
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = (int) $this->request->getGet('district_id');
        }
    }

    private function setBlockId(array &$data): void
    {
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = (int) $this->request->getGet('block_id');
        }

        if ($this->user->agency_type_id === $this->settings->block_user) {
            $data['block_id'] = (int) $this->user->block_id;
        }
    }

    private function getMprComponents(ReportsModel $reportModel, array $data): array
    {
        $filter = [
            'month_id' => $data['month_id'],
            'year_id' => $data['year_id'],
        ];

        if (isset($data['user'])) {
            $filter['user_id'] = $data['user']->id;
        }

        if ($data['district_id']) {
            $filter['district_id'] = $data['district_id'];
        }

        if ($data['block_id']) {
            $filter['block_id'] = $data['block_id'];
            $filter['user_group'] = $this->settings->block_user;
        }

        if ($data['agency_type_id']) {
            $filter['agency_type_id'] = $data['agency_type_id'];
        }

        return $reportModel->getMpr($filter);
    }

    private function setDownloadUrl(array &$data): void
    {
        $data['download_url'] = Url::mprDownload . '?year=' . $data['year_id'] . '&month=' . $data['month_id'] . '&agency_type_id=' . $data['agency_type_id'];
    }
}
