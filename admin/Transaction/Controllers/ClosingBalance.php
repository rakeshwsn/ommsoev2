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

        if ($this->user->agency_type_id === $this->settings->block_user || $this->user->agency_type_id === $this->settings->cbo_user) {
            $filter['block_id'] = $this->user->block_id;
        }

        if ($this->user->agency_type_id === $this->settings->district_user) {
            $filter['district_id'] = $this->user->district_id;
        }

        $data['months'] =  $this->cbModel->getLedgerReport($filter);

        foreach ($data['months'] as &$month) {
            $month->status = $this->setStatusLabel($month->status);
            $month->edit_url = $this->setEditUrl($month);
        }

        $data['year_id'] = $yearId;
        $data['agency_type_id'] = $agencyTypeId;
        $data['fund_agencies'] = $this->setFundAgencies($fundAgencyId);
        $data['agency_types'] = $this->setAgencyTypes();
        $data['message'] = $this->session->getFlashdata('message');

        return $this->template->view('Admin\Transaction\Views\closingbalance', $data);
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
        return Url::closingBalanceEdit
            . '/' . $month['year_id']
            . '/' . $month['month_id']
            . '/' . $month['agency_type_id']
            . '/' . $month['fund_agency_id'];
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

    private function setFundAgencies(?int $fundAgencyId): array
    {
        $blockModel = new BlockModel();

        if ($this->user->agency_type_id === $this->settings->block_user || $this->user->agency_type_id === $this->settings->cbo_user) {
            $filter = ['block_id' => $this->user->block_id];
        } else {
            $filter = [];
        }

        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }

        $fundAgencies = $blockModel->getFundAgencies($filter);

        return array_combine(array_column($fundAgencies, 'fund_agency_id'), array_column($fundAgencies, 'name'));
    }

    private function setAgencyTypes(): array
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
    ):
