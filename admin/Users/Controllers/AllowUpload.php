<?php
namespace Admin\Users\Controllers;
use Admin\Common\Models\AllowuploadModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use Config\Url;

class AllowUpload extends AdminController{
	private $error = array();

	public function index(){

	    //datepicker
        $this->template->add_package(['datepicker'],true);

	    $year = getCurrentYearId();
	    $month = getCurrentMonthId();

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

	    if($month && ($district_id || $agency_type_id)){
            $userModel = new UserModel();
	        $filter = [
	            'year' => $year,
	            'month' => $month,
	            'district_id' => $district_id,
	            'agency_type_id' => $agency_type_id,
            ];
            $users = $userModel->getUserUploadMonths($filter);

            foreach($users as $user){
                $data['users'][] = [
                    'upload_id' => $user->upload_id,
                    'district' => $user->district,
                    'block' => $user->block,
                    'user_id' => $user->user_id,
                    'firstname' => $user->firstname,
                    'from_date' => ymdToDmy($user->from_date),
                    'to_date' => ymdToDmy($user->to_date),
                    'extended_date' => $user->extended_date ? ymdToDmy($user->extended_date):'',
                ];
            }
        }

        $data['year_id'] = $year;
        $data['month_id'] = $month;
        $data['district_id'] = $district_id;
        $data['agency_type_id'] = $agency_type_id;
        $data['allow_upload_url'] = Url::userAllowUpload;

        return $this->template->view('Admin\Users\Views\allow_upload',$data);

	}

    public function update() {
        $user_id = $this->request->getPost('user_id');
        $upload_id = $this->request->getPost('upload_id');
        $to_date = $this->request->getPost('to_date');

        $allowuploadModel = new AllowuploadModel();

        $id = $allowuploadModel->extendDate([
            'user_id' => $user_id,
            'upload_id' => $upload_id,
            'to_date' => dmyToYmd($to_date),
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