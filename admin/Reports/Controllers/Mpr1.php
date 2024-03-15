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
        $defaultValues = $this->setDefaultValues();

        $this->setYearId($defaultValues);
        $this->setMonthId($defaultValues);
        $this->setAgencyTypeId($defaultValues);
        $this->setDistrictId($defaultValues);
        $this->setBlockId($defaultValues);

        $reportModel = new ReportsModel();
        $mprComponents = $this->getMprComponents($reportModel, $defaultValues);
        $defaultValues['components'] = $this->buildTree($mprComponents, 'parent', 'component_id');
        $defaultValues['components'] = $this->getTable($defaultValues['components'], 'view');

        $this->setDownloadUrl($defaultValues);

        return $this->template->view('Admin\Reports\Views\mpr_block', $defaultValues);
    }

    private function setDefaultValues(): array
    {
        return [
            'yearId' => $this->getCurrentYearId(),
            'monthId' => $this->getMonthIdByMonth(date('m')),
            'agencyTypeId' => null,
            'districtId' => null,
            'blockId' => null,
            'agencyTypes' => [],
            'districts' => [],
            'blocks' => [],
            'fundAgencies' => [],
            'years' => $this->getAllYears(),
            'months' => $this->getAllMonths(),
            'filterPanel' => '',
            'downloadUrl' => '',
        ];
    }

    private function setYearId(array &$data): void
    {
        if ($year = $this->request->getGet('year')) {
            $data['yearId'] = (int) $year;
        }
    }

    private function setMonthId(array &$data): void
    {
        if ($month = $this->request->getGet('month')) {
            $data['monthId'] = (int) $month;
        }
    }

    private function setAgencyTypeId(array &$data): void
    {
        if ($agencyTypeId = $this->request->getGet('agency_type_id')) {
            $data['agencyTypeId'] = (int) $agencyTypeId;
        }
    }

    private function setDistrictId(array &$data): void
    {
        if ($districtId = $this->request->getGet('district_id')) {
            $data['districtId'] = (int) $districtId;
        }
    }

    private function setBlockId(array &$data): void
    {
        if ($blockId = $this->request->getGet('block_id')) {
            $data['blockId'] = (int) $blockId;
        }

        if ($this->user->agency_type_id === $this->settings->block_user) {
            $data['blockId'] = (int) $this->user->block_id;
        }
    }

    private function getMprComponents(ReportsModel $reportModel, array $data): array
    {
        $filter = [
            'month_id' => $data['monthId'],
            'year_id' => $data['yearId'],
        ];

        if (isset($data['user'])) {
            $filter['user_id'] = $data['user']->id;
        }

        if ($data['districtId']) {
            $filter['district_id'] = $data['districtId'];
        }

        if ($data['blockId']) {
            $filter['block_id'] = $data['blockId'];
            $filter['user_group'] = $this->settings->block_user;
        }

        if ($data['agencyTypeId']) {
            $filter['agency_type_id'] = $data['agencyTypeId'];
        }

        return $reportModel->getMpr($filter);
    }

    private function setDownloadUrl(array &$data): void
    {
        $data['downloadUrl'] = Url::mprDownload . '?year=' . $data['yearId'] . '&month=' . $data['monthId'] . '&agency_type_id=' . $data['agencyTypeId'];
    }
}
