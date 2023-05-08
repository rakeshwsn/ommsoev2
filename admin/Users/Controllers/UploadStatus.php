<?php
namespace Admin\Users\Controllers;
use Admin\Common\Models\AllowuploadModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use Config\Url;

class UploadStatus extends AdminController{
	private $error = array();

	public function index(){

	    $year = getCurrentYearId();
	    $month = getMonthIdByMonth(date('m'));

	    if($this->request->getGet('year')){
	        $year = $this->request->getGet('year');
        }
	    if($this->request->getGet('month')){
	        $month = $this->request->getGet('month');
        }
        $district_id = '';
	    if($this->request->getGet('district_id')){
	        $district_id = $this->request->getGet('district_id');
        }
        $agency_type_id = '';
	    if($this->request->getGet('agency_type_id')){
	        $agency_type_id = $this->request->getGet('agency_type_id');
        }

        $data['districts'] = (new DistrictModel())->asArray()->findAll();

        $data['agency_types'] = (new UserGroupModel())->whereIn('id', [5,7,8, 9])
            ->orderBy('name')->asArray()->findAll();

        $data['users'] = [];

	    if($month && $district_id){
            $userModel = new UserModel();
	        $filter = [
	            'year' => $year,
	            'month' => $month,
	            'district_id' => $district_id,
	            'agency_type_id' => $agency_type_id,
            ];
            $users = $userModel->getUploadStatus($filter);

            foreach($users as $user){
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

        return $this->template->view('Admin\Users\Views\upload_status',$data);

	}

    public function update() {

        $upload_id = $this->request->getPost('upload_id');
        $status = $this->request->getPost('status');
        $modulecode = $this->request->getPost('modulecode');

        $allowuploadModel = new AllowuploadModel();

        $id = $allowuploadModel->updateStatus([
            'upload_id' => (int)$upload_id,
            'status' => $status,
            'modulecode' => $modulecode,
        ]);

        if($id){
            $json_data = [
                'status' => true
            ];
        } else {
            $json_data = [
                'status' => false
            ];
        }
        return $this->response->setJSON($json_data);

	}

}