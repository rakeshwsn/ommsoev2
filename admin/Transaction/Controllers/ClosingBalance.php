<?php

declare(strict_types=1);

namespace Admin\Transaction\Controllers;

use Admin\Common\Models\AllowuploadModel;
use Admin\Common\Models\MonthModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Transaction\Models\ClosingbalanceModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use CodeIgniter\HTTP\URI;

class ClosingBalance extends AdminController
{
    private ClosingbalanceModel $cbModel;
    private MonthModel $monthModel;

    public function __construct()
    {
        $this->cbModel = new ClosingbalanceModel();
        $this->monthModel = new MonthModel();
    }

    public function index(): string
    {
        $this->checkPermission();

        $yearId = $this->request->getGet('year') ?? getCurrentYearId();
        $agencyTypeId = $this->request->getGet('agency_type_id') ?? $this->user->agency_type_id;
        $fundAgencyId = $this->request->getGet('fund_agency_id') ?? $this->user->fund_agency_id;

        $filter = [
            'year' => $yearId,
            'agency_type_id' => $agencyTypeId,
            'fund_agency_id' => $fundAgencyId,
            'user_id' => $this->user->user_id,
        ];

        $this->applyUserFilter($filter);

        $data['months'] =  $this->cbModel->getLedgerReport($filter);

        $this->addStatusAndUrlToMonths($data['months']);

        $data['year_id'] = $yearId;
        $data['agency_type_id'] = $agencyTypeId;
        $data['fund_agencies'] = $this->getFundAgencies($fundAgencyId);
        $data['agency_types'] = $this->getAgencyTypes();
        $data['message'] = $this->session->getFlashdata('message');

        return $this->template->view('Admin\Transaction\Views\closingbalance', $data);
    }

    private function addStatusAndUrlToMonths(array &$months): void
    {
        foreach ($months as &$month) {
            $month->status = $this->setStatusLabel($month->status);
            $month->edit_url = $this->setEditUrl($month);
        }
    }

    private function setStatusLabel(?int $status): string
    {
        if ($status === null) {
            return '';
        }

        $statuses = [
            0 => 'warning',
            1 => 'success',
            2 => 'danger',
            3 => 'info',
        ];

        return '<label class="badge badge-' . $statuses[$status] . '">' . $this->statuses[$status] . '</label>';
    }

    private function setEditUrl(array $month): string
    {
        return (new URI($this->request))
            ->setPath(Url::closingBalanceEdit)
            ->setSegment(4, $month['year_id'])
            ->setSegment(5, $month['month_id'])
            ->setSegment(6, $month['agency_type_id'])
            ->setSegment(7, $month['fund_agency_id'])
            ->getURL();
    }

    private function checkPermission(): void
    {
        if (
            $this->user->agency_type_id === $this->settings->ps_user
            || $this->user->agency_type_id === $this->settings->rs_user
        ) {
            $this->agency();
        }
    }

    private function applyUserFilter(array &$filter): void
    {
        if (in_array($this->user->agency_type_id, [$this->settings->block_user, $this->settings->cbo_user])) {
            $filter['block_id'] = $this->user->block_id;
        }

        if ($this->user->agency_type_id === $this->settings->district_user) {
            $filter['district_id'] = $this->user->district_id;
        }
    }

    private function getFundAgencies(?int $fundAgencyId): array
    {
        $blockModel = new BlockModel();

        $filter = [];

        if ($this->user->agency_type_id === $this->settings->block_user || $this->user->agency_type_id === $this->settings->cbo_user) {
            $filter['block_id'] = $this->user->block_id;
        }

        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }

        $fundAgencies = $blockModel->getFundAgencies($filter);

        return array_combine(array_column($fundAgencies, 'fund_agency_id'), array_column($fundAgencies, 'name'));
    }

    private function getAgencyTypes(): array
    {
        if ($this->user->agency_type_id === $this->settings->block_user) {
            $agencyTypes = (new UserGroupModel())->getBlockUsers();
        } else {
            $agencyTypes = (new UserGroupModel())->findAll();
        }

        return array_combine(array_column($agencyTypes, 'user_group_id'), array_column($agencyTypes, 'name'));
    }

    public function info(
        int $year,
        int $month,
        int $agencyTypeId,
        int $fundAgencyId
    ): void
    {
        // Add your info action code here
    }
}
