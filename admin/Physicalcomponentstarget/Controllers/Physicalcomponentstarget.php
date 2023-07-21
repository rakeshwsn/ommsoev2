<?php

namespace Admin\Physicalcomponentstarget\Controllers;

use Admin\Permission\Models\PermissionModel;
use Admin\PhysicalComponents\Models\ComponentPhyModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Physicalcomponentstarget\Models\PcmTargetModel;
use App\Controllers\AdminController;

class Physicalcomponentstarget extends AdminController
{
    private $error = array();
    private $usergroupModel;
    private $enterprisesModel;

    public function __construct()
    {
        $this->physicalcomponents = new ComponentPhyModel();
        $this->pcmTarget = new PcmTargetModel();
    }

    public function index()
    {
        $data = [];
        $user  = service('user');
        // echo $user->district_id; exit;
        $data['year_id'] = 1;

        // $data['year_id'] = '';
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }
        // fetch data for form dropdown
        $data['years'] = getAllYears();
        $districtModel = new DistrictModel();
        $data['districts_main'] = $districtModel->getAll();
        //  printr($data); exit;
        $data['add'] = admin_url('physicalcomponentstarget/add');
        $data['addachForm'] = admin_url('physicalachievement/add');

        $filter = [
            'year_id' => $data['year_id'],
            'district_id' => $user->district_id,

        ];
        $data['selectedYear'] = 1;
        if ($this->request->getGet('year_id')) {
            $data['selectedYear'] = $this->request->getGet('year_id');
        }

        $data['checkExists'] = $this->pcmTarget->showCheckExistsData($filter);
        $data['componentsAll'] = $this->physicalcomponents->getAll();
        $this->getTableHeaders($data);
        $results = $this->pcmTarget->showTargetComponents($filter);
        // printr($results); exit;
        foreach ($results as  $result) {
            $data['target_data'][$result['district']][] = [
                'name' => $result['description'],
                'total' => $result['total'],
                'district' => $result['district'],
                'mprcomponents_master_id' => $result['mprcomponents_master_id'],

            ];
        }

        //printr($data['target_data']); exit;

        return $this->template->view('Admin\Physicalcomponentstarget\Views\componenttargetdata', $data);
    }

    private function getTableHeaders(&$data)
    {
        $headers = ['Sl No', 'District'];
        foreach ($data['componentsAll'] as $row) {
            $components_name = $row['description'];
            if (!in_array($components_name, $headers)) {
                $headers[] = $components_name;
            }
        }
        $data['headers'] = $headers;
    }

    public function add()
    {

        $this->template->set_meta_title('Physical Components Target');

        if ($this->request->getMethod(1) === 'POST') {
            // printr($this->request->getPost());
            // exit;
            $this->pcmTarget->addPhysicaltargetdata($this->request->getPost());
            $this->session->setFlashdata('message', 'Physical Targets added Successfully.');

            return redirect()->to(base_url('admin/physicalcomponentstarget'));
        }
        $this->getForm();
    }

    public function edit()
    {

        $this->template->set_meta_title('Components');

        if ($this->request->getMethod(1) === 'POST') {
            // $id = $this->uri->getSegment(4);
            // printr($this->request->getPost());
            // exit;
            $this->pcmTarget->updateMasterData($this->request->getPost());
            $this->session->setFlashdata('message', 'Physicalcomponents Updated Successfully.');

            return redirect()->to(base_url('admin/physicalcomponentstarget'));
        }
        $this->getForm();
    }

    public function delete()
    {
        if ($this->request->getPost('selected')) {
            $selected = $this->request->getPost('selected');
        } else {
            $selected = (array) $this->uri->getSegment(4);
        }
        $this->physicalcomponents->delete($selected);
        $this->session->setFlashdata('message', 'Physicalcomponents deleted Successfully.');
        return redirect()->to(base_url('admin/physicalcomponents'));
    }

    protected function getForm()
    {

        $this->template->add_package(array('select2'), true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'Physical Components Target',
            'href' => admin_url('physicalcomponentstarget')
        );
        $data['years'] = getAllYears();
        $districtModel = new DistrictModel();
        $data['districts_main'] = $districtModel->getAll();
        $data['componentsAll'] = $this->physicalcomponents->getAll();

        $data['heading_title']     = 'Physical Components Target';
        $data['text_form'] = $this->uri->getSegment(4) ? "physical Components Edit" : "physical Components Add";
        $data['cancel'] = admin_url('physicalcomponents');

        if (isset($this->error['warning'])) {
            $data['error']     = $this->error['warning'];
        }
        $data['components'] = $this->physicalcomponents->getComponents();
        $districtModel = new DistrictModel();
        $data['districts_main'] = $districtModel->getAll();
        $data['main_master'] = [];
        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $data['main_master'] = $components_info = $this->pcmTarget->getTargetcomponent();
        }

        // printr($data['main_master']);mprcomponents_master_id
        //  exit;

        echo $this->template->view('Admin\Physicalcomponentstarget\Views\componenttargetdataForm', $data);
    }





    protected function validateForm()
    {
        //printr($_POST);
        $validation =  \Config\Services::validation();
        $id = $this->uri->getSegment(4);
        $regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor

        $rules = $this->usergroupModel->validationRules;

        if ($this->validate($rules)) {
            return true;
        } else {
            //printr($validation->getErrors());
            $this->error['warning'] = "Warning: Please check the form carefully for errors!";
            return false;
        }
        return !$this->error;
    }
}
/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */