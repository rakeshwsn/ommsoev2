<?php

namespace Admin\Users\Controllers;

use Admin\Common\Models\AllowuploadModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use Config\Services;
use Config\Url;

class AllowUpload extends AdminController
{
    /**
     * @var AllowuploadModel
     */
    private $allowuploadModel;

    /**
     * @var DistrictModel
     */
    private $districtModel;

    /**
     * @var UserGroupModel
     */
    private $userGroupModel;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var array
     */
    private $error = [];

    /**
     * AllowUpload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->allowuploadModel = new AllowuploadModel();
        $this->districtModel = new DistrictModel();
        $this->userGroupModel = new UserGroupModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display the allow upload page
     */
    public function index(): string
    {
        //datepicker
        $this->template->addPackage(['datepicker'], true);

        $year = getCurrentYearId();
        $month = getCurrentMonthId();
        $districtId = $this->request->getGet('district_id');
        $agencyTypeId = $this->request->getGet('agency_type_id');

        $data['districts'] = $this->districtModel->findAll();
        $data['agency_types'] = $this->userGroupModel
            ->whereIn('id', [5, 7, 8, 9])
            ->orderBy('name')
            ->findAll();
        $data['users'] = [];

        if ($month && ($districtId || $agencyTypeId)) {
            $filter = [
                'year' => $year,
                'month' => $month,
                'district_id' => $districtId,
                'agency_type_id' => $agencyTypeId,
            ];
            $users = $this->userModel->getUserUploadMonths($filter);

            foreach ($users as $user) {
                $data['users'][] = [
                    'upload_id' => $user->upload_id,
                    'district' => $user->district,
                    'block' => $user->block,
                    'user_id' => $user->user_id,
                    'firstname' => $user->firstname,
                    'from_date' => $user->from_date->format('d/m/Y'),
                    'to_date' => $user->to_date->format('d/m/Y'),
                    'extended_date' => $user->extended_date ? $user->extended_date->format('d/m/Y') : '',
                ];
            }
        }

        $data['year_id'] = $year;
        $data['month_id'] = $month;
        $data['district_id'] = $districtId;
        $data['agency_type_id'] = $agencyTypeId;
        $data['allow_upload_url'] = Url::userAllowUpload;

        return $this->template->view('Admin\Users\Views\allow_upload', $data);
    }

    /**
     * Update the allow upload date
     *
     * @param int $userId
     * @param int $uploadId
     * @param string $toDate
     * @return \CodeIgniter\HTTP\Response
     */
    public function update(int $userId, int $uploadId, string $toDate): \CodeIgniter\HTTP\Response
    {
        $validation = Services::validation();
        $validation->setRules([
            'user_id' => 'required|integer',
            'upload_id' => 'required|integer',
            'to_date' => 'required|date',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return Services::response()->setJSON([
                'status' => false,
                'errors' => $validation->getErrors(),
            ])->setStatusCode(400);
        }

        $id = $this->allowuploadModel->extendDate([
            'user_id' => $userId,
            'upload_id' => $uploadId,
            'to_date' => dmyToYmd($toDate),
        ]);

        if ($id) {
            return Services::response()->setJSON([
                'status'
