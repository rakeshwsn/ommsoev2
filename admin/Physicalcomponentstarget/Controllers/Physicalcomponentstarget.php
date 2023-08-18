<?php

namespace Admin\Physicalcomponentstarget\Controllers;

use Admin\Permission\Models\PermissionModel;
use Admin\Physicalcomponents\Models\ComponentPhyModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Physicalcomponentstarget\Models\PcmTargetModel;
use Admin\Common\Models\YearModel;
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
        $this->years = new YearModel();
    }

    public function index()
    {
        $data = [];
        $user  = service('user');
        $data['year_id'] = getCurrentYearId();
        $data['allYears'] =  $this->years->withDeleted()->where('id >', 1)->findAll();
        // $data['year_id'] = '';
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        $data['district_id'] = '';
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }
        $data['years'] = getAllYears();
        $districtModel = new DistrictModel();
        $data['districts_main'] = $districtModel->getAll();
        //  printr($data); exit;
        $data['add'] = admin_url('physicalcomponentstarget/add');
        $data['addachForm'] = admin_url('physicalachievement/add');

        $filter = [
            'year_id' => $data['year_id'],
            'district_id' => $user->district_id,
            'fund_agency_id' => $user->fund_agency_id,

        ];
        $data['selectedYear'] = 1;
        if ($this->request->getGet('year_id')) {
            $data['selectedYear'] = $this->request->getGet('year_id');
        }

        $data['checkExists'] = $this->pcmTarget->showCheckExistsData($filter);
        $data['componentsAll'] = $this->physicalcomponents->getAllComponentData($filter);
        //printr($data['componentsAll']); exit;
        $this->getTableHeaders($data);
        $results = $this->pcmTarget->showTargetComponents($filter);
         //printr($results); exit;
        foreach ($results as  $result) {
            $data['target_data'][$result['district']][] = [
                'name' => $result['description'],
                'total' => $result['total'],
                'district' => $result['district'],
                'mprcomponents_master_id' => $result['mprcomponents_master_id'],

            ];
        }
// printr($data['target_data']); exit;


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
        $headers[] = 'Total';
        $data['headers'] = $headers;
    }

    public function add()
    {
        $this->template->set_meta_title('Physical Components Target');
        if ($this->request->getMethod(1) === 'POST') {
            // printr($this->request->getPost());
            // exit;

           $dataExist =  $this->pcmTarget->addPhysicaltargetdata($this->request->getPost());
           //echo $dataExist; exit;
           if($dataExist == 0){
            $this->session->setFlashdata('message', 'Physical Targets added already exists Please Edit.');
           } else {
            $this->session->setFlashdata('message', 'Physical Targets added Successfully.');
           }


            return redirect()->to(base_url('admin/physicalcomponentstarget'));
        }
        $this->getForm();
    }

    public function edit()
    {
        $this->template->set_meta_title('Components');

        if ($this->request->getMethod(1) === 'POST') {
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
        };
        $filter = [
            'year_id' => 2,
        ];

        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }
        $data['components'] = $this->physicalcomponents->getAllComponentData($filter);
        $districtModel = new DistrictModel();
        $data['districts_main'] = $districtModel->getAll();
        $data['main_master'] = [];
        $data['editYear'] = '';
        $data['allYears'] =  $this->years->withDeleted()->where('id >', 1)->findAll();
        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $masterInfo =  $this->pcmTarget->find($this->uri->getSegment(4));
            //printr($masterInfo); exit;
            $data['editYear'] = $masterInfo->year_id;

            //printr($data['editYear']); exit;
        }
        echo $this->template->view('Admin\Physicalcomponentstarget\Views\componenttargetdataForm', $data);
    }


    public function searchtargetdata()
    {
        $yearId = $_POST['year_id'];
        $filter = [
            'year_id' => $yearId,
        ];

        $components = $this->physicalcomponents->getAllComponentData($filter);
        $districtModel = new DistrictModel();
        $districts_main =  $this->pcmTarget->showTargetDistrict();
        $main_masters  = $this->pcmTarget->getTargetcomponent($filter);
        $temp_district = [];

      // printr($main_masters); exit;


        $tableHeaderHtml = '<thead><tr>
        <td>District</td>';
        foreach ($components as $component) {
            $tableHeaderHtml .= '<td height="101">' . $component['description'] . '</td>';
        }
        $tableHeaderHtml .= '<td height="101">Total</td></tr></thead>';
        $tableBodyHtml = '';
        foreach ($districts_main as $key => $districts_mains) {
            $tableBodyHtml .= '<tr>';
            $tableBodyHtml .= '<td><input type="hidden" name="component['. $key .'][district_id]"  value="' . $districts_mains->district_id . '"><input type="hidden" name="component['. $key .'][fund_agency_id]"  value="' . $districts_mains->fund_agency_id . '">' . $districts_mains->district_formatted . '</td>';
            foreach ($components as $component) {
                $value = '';
                foreach ($main_masters as $item) {
                    if ($item['district_id'] == $districts_mains->district_id &&
                $item['mc_id'] == $component['id'] &&
                $item['fund_agency_id'] == $districts_mains->fund_agency_id) {
                $value = $item['total'];
                break;
            }
                }
                $tableBodyHtml .= '<td><input type="number" name="component['. $key .'][data][' . $component['id'] . ']" class="crop-input form-control" oninput="calculateTotals()" value="' . $value . '"></td>';
            }
            $tableBodyHtml .= '<td><span class="total-value"></span></td>';
            $tableBodyHtml .= '</tr>';
        }
        $tableBodyHtml .= '<tr class="text-right"><td colspan="' . (count($components) + 1) . '"><button id="submitButton" class="btn btn-alt-primary">Submit</button></td></tr>';
        $tableBodyHtmlfinal = $tableHeaderHtml . '<tbody>' . $tableBodyHtml . '</tbody>';
        echo $tableBodyHtmlfinal;
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