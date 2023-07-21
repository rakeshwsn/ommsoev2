<?php

namespace Admin\Physicalachievement\Controllers;

use Admin\Permission\Models\PermissionModel;
use Admin\PhysicalComponents\Models\ComponentPhyModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Physicalcomponentstarget\Models\PcmTargetModel;
use Admin\Physicalachievement\Models\PhysicalachievementModel;
use App\Controllers\AdminController;

class Physicalachievement extends AdminController
{
    private $error = array();
    private $usergroupModel;
    private $enterprisesModel;

    public function __construct()
    {
        $this->physicalcomponents = new ComponentPhyModel();
        $this->pcmTarget = new PcmTargetModel();
        $this->pcmachieve = new PhysicalachievementModel();
    }

    public function index()
    {

        $data = [];
        $user  = service('user');
        $data['year_id'] = 2;
        $data['get_months'] = getMonths();
        //$data['monthId'] = $this->request->getGet('month_id');
        $currentMonth = date('n') - 1;
        $data['monthId'] =  $currentMonth;
        if ($this->request->getGet('month_id')) {
            $data['monthId'] = $this->request->getGet('month_id');
        }
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
        $data['addach'] = admin_url('physicalachievement/add');
        $filter = [
            'year_id' => $data['year_id'],
            'district_id' => $user->district_id,
            'month_id' => $data['monthId'],

        ];
        $data['selectedYear'] = 1;
        if ($this->request->getGet('year_id')) {
            $data['selectedYear'] = $this->request->getGet('year_id');
        }
        $data['componentsAll'] = $compo = $this->pcmachieve->getMprComponentsall();
        $this->getTableHeaders($data);
        //printr($filter); exit;
        $data['results'] = $results = $this->pcmachieve->showTargetAchData($filter);

        $data['target_acv_data'] = array();

        foreach ($results as $result) {
            $arraysecond = array();
            foreach ($compo as $compos) {
                $targetKey = 'target' . $compos['id'];
                $uptoAchKey = 'upto_ach' . $compos['id'];
                $curAchKey = 'cur_ach' . $compos['id'];

                $arraysecond[$targetKey] = isset($result[$targetKey]) ? $result[$targetKey] : 0;
                $arraysecond[$uptoAchKey] = isset($result[$uptoAchKey]) ? $result[$uptoAchKey] : 0;
                $arraysecond[$curAchKey] = isset($result[$curAchKey]) ? $result[$curAchKey] : 0;
            }

            $data['target_acv_data'][] = array(
                'district_id' => $result['district_id'],
                'district' => $result['district'],
                'total_block' => $result['total_block'],
                'arraysecond' => $arraysecond,
            );
        }
        // printr($data['target_acv_data']);
        // exit;
        return $this->template->view('Admin\Physicalachievement\Views\componentachievedata', $data);
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

        $this->template->set_meta_title('Physical Components Physicalcomponentachievement');

        if ($this->request->getMethod(1) === 'POST') {
            // printr($this->request->getPost());
            // exit;
            $this->pcmachieve->addPhysicalachData($this->request->getPost());
            $this->session->setFlashdata('message', 'Physical Targets added Successfully.');

            return redirect()->to(base_url('admin/physicalachievement'));
        }
        $this->getForm();
    }

    public function edit()
    {

        $this->template->set_meta_title('Components');

        if ($this->request->getMethod(1) === 'POST') {
            $id = $this->uri->getSegment(4);

            $this->physicalcomponents->update($id, $this->request->getPost());
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
        $data['current_year_id'] = 1; //getCurrentYearId();
        $data['get_months'] = getMonths();
        $districtModel = new DistrictModel();
        $data['districts'] = $districtModel->getAll();
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
        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            // $data['main_master'] =$components_info = $this->pcmTarget->getTargetcomponent($this->uri->getSegment(4));

        }

        // printr($data);
        //  exit;

        echo $this->template->view('Admin\Physicalachievement\Views\componentacvievedataForm', $data);
    }


    public function searchtargetdata()
    {

        $user  = service('user');
        $usercheck = $user->district_id;

        $districtId = $_POST['district_id'];
        $yearId = $_POST['year_id'];
        $monthId = $_POST['month_id'];
        $monthName = $_POST['month_name'];
        $yearName = $_POST['year_name'];
        $currentYearId = 1;

        $filter = [
            'year_id' => $yearId,
            'district_id' => $districtId,
            'month_id' => $monthId,

        ];
        $currentYearStartMonth = 4;
        $currentMonth = date('n');
        $currentYear = 1;


        $nextYearAprilMonth = 4;

        $checkExistsmonthData = $this->pcmachieve->showGetExsistsMonthData($filter);
        // printr(!empty($checkExistsmonthData->month_id)) ; exit;

        // $day = date('j');
        // if ($day <= 1 || $day >= 5) {
        //     echo '<h3 style="text-align: center; margin-top: 20px;">Fill-up month date should be from 1 to 5.</h3>';
        //     exit;
        // }
        if($currentYearId != $yearId){
            echo '<h3 style="text-align: center; margin-top: 20px;">Selected year not valid.</h3>';
            exit;
        } else {
        if (!empty($usercheck)) {
            if (!empty($checkExistsmonthData) && $checkExistsmonthData->month_id == $monthId) {
                echo '<h3 style="text-align: center; margin-top: 20px;">Selected month already Has Data.</h3>';
                exit;
            }
            //  printr($checkExistsmonthData); exit;
            if ($yearId == $currentYear && ($monthId >= $currentMonth || $monthId < $nextYearAprilMonth)) {

                echo '<h3 style="text-align: center; margin-top: 20px;">Selected month is not allowed.</h3>';
                exit;
            }
        } else if (empty($usercheck)) {
            if ($yearId == $currentYear && ($monthId >= $currentMonth || $monthId < $nextYearAprilMonth)) {

                echo '<h3 style="text-align: center; margin-top: 20px;">Selected month is not allowed.</h3>';
                exit;
            }
        }
        $checkExistsData = $this->pcmTarget->showGetExsistsData($filter);
        // printr($checkExistsData); exit;
        $components = $this->physicalcomponents->getComponents();
        $componentsData = $this->pcmachieve->getComponentsAllData($filter);
        // printr($componentsData); exit;
        $tableHeaderHtml = '<thead><tr>
                        <th>Components</th>
                        <th>Target for '. $yearName .'</th>
                        <th>UP To This Month</th>
                        <th>'. $monthName .'</th>
                        <th>Cumulative</th>
                    </tr></thead>';
        $tableBodyHtml = '';
        foreach ($componentsData as $component) {
            $tableBodyHtml .= '<tr>';
            $tableBodyHtml .= '<td>' . $component['component'] . '</td>';
            $tableBodyHtml .= '<td class="targettotal">' . $component['targettotal'] . '</td>';
            $tableBodyHtml .= '<td class="achTotal">' . $component['ach_total'] . '</td>';
            $tableBodyHtml .= '<td><input type="number" name="component[' . $component['id'] . ']" class="form-control currentMonth" id="currentMonth" value="' . $component['cur_total'] . '"></td>';
            $tableBodyHtml .= '<td><input type="text" name="" class="form-control cumulative" id="cumulative" readonly></td>';
            $tableBodyHtml .= '<td class="message"></td>';
            $tableBodyHtml .= '<input type="hidden" name="monthid" value="' . $monthId . '">';
            $tableBodyHtml .= '<input type="hidden" name="componentidmain" value="' . $checkExistsData->id . '">';
            $tableBodyHtml .= '</tr>';
        }
        $tableBodyHtml .= '<tr class="text-right"><td colspan="5"><button id="submitButton" class="btn btn-alt-primary">Submit</button></td></tr>';
        $tableBodyHtmlfinal = $tableHeaderHtml . '<tbody>' . $tableBodyHtml . '</tbody>';
        // Echo the updated table body HTML
        echo $tableBodyHtmlfinal;
    }
    }
}
/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */