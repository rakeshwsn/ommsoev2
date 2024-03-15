<?php

namespace Admin\Users\Controllers;

use Admin\Common\Models\AllowuploadModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use Config\Url;

class UploadStatus extends AdminController
{
    /**
     * @var array
     */
    private $error = [];

    /**
     * Index method
     */
    public function index()
    {
        $year = $this->validateYear();
        $month = $this->validateMonth();
        $district_id = $this->validateDistrictId();
        $agency_type_id = $this->validateAgencyTypeId();

        $data['districts'] = (new DistrictModel())->asArray()->findAll();
        $data['agency_types'] = (new UserGroupModel())->whereIn('id', [5, 7, 8, 9])
            ->orderBy('name')->asArray()->findAll();
        $data['users'] = [];

        if ($month && $district_id) {
            $userModel = new UserModel();
            $filter = [
                'year' => $year,
                'month' => $month,
                'district_id' => $district_id,
                'agency_type_id' => $agency_type_id,
            ];
            $users = $userModel->getUploadStatus($filter);

            foreach ($users as $user) {
                $data['users'][] = [
                    'upload_id' => $user->id,
                    'district' => $user->district,
                    'block' => $user->block,
                    'user_id' => $user->user_id,
                    'firstname' => $user->firstname,
                    'status' => $user->status,
                    'module' => $user->module,
                    'modulecode' => $user->modulecode,
                    'date_added' => ymdToDmy($user->created_at),
                ];
            }
        }

        $data['year_id'] = $year;
        $data['month_id'] = $month;
        $data['district_id'] = $district_id;
        $data['status_update_url'] = Url::userUploadStatus;
        $data['statuses'] = $this->statuses;

        return $this->template->view('Admin\Users\Views\upload_status', $data);
    }

    /**
     * Validate year input
     *
     * @return int
     */
    private function validateYear(): int
    {
        $year = (int)($this->request->getGet('year') ?? getCurrentYearId());
        if ($year < 2022 || $year > date('Y')) {
            $year = getCurrentYearId();
        }
        return $year;
    }

    /**
     * Validate month input
     *
     * @return int
     */
    private function validateMonth(): int
    {
        $month = (int)($this->request->getGet('month') ?? date('m'));
        if ($month < 1 || $month > 12) {
            $month = date('m');
        }
        return $month;
    }

    /**
     * Validate district_id input
     *
     * @return int
     */
    private function validateDistrictId(): int
    {
        $district_id = (int)($this->request->getGet('district_id') ?? '');
        if ($district_id < 1) {
            $district_id = '';
        }
        return $district_id;
    }

    /**
     * Validate agency_type_id input
     *
     * @return int
     */
    private function validateAgencyTypeId(): int
    {
        $agency_type_id = (int)($this->request->getGet('agency_type_id') ?? '');
        if ($agency_type_id < 1) {
            $agency_type_id = '';
        }
        return $agency_type_id;
    }

    /**
     * Update method
     */
    public function update()
    {
        $upload_id = (int)$this->request->getPost('
