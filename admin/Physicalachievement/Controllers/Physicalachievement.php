<?php

namespace Admin\Physicalachievement\Controllers;

use Admin\Common\Models\YearModel;
use Admin\Permission\Models\PermissionModel;
use Admin\Physicalcomponents\Models\ComponentPhyModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Physicalcomponentstarget\Models\PcmTargetModel;
use Admin\Physicalachievement\Models\PhysicalachievementModel;
use App\Controllers\AdminController;
use DateTime;

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
        $this->years = new YearModel();
    }

    public function index()
    {

        $data = [];
        $user  = service('user');
        $data['year_id'] = getCurrentYearId();
        $data['allYears'] =  $this->years->withDeleted()->where('id >', 1)->findAll();
        $data['get_months'] = getMonths();
        //$data['monthId'] = $this->request->getGet('month_id');
        $currentMonth = date('n') - 1;
        $data['monthId'] =  $currentMonth;


        $data['getMonths'] = getMonthById(getMonthIdByMonth($currentMonth));
        if ($this->request->getGet('month_id')) {
            $data['getMonths'] = getMonthById(getMonthIdByMonth($this->request->getGet('month_id')));
        }
        // printr($getMonths['name']); exit;

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
        $data['selectedYear'] = 2;
        if ($this->request->getGet('year_id')) {
            $data['selectedYear'] = $this->request->getGet('year_id');
        }
        $data['checkExists'] = $this->pcmTarget->showCheckExistsData($filter);
        $data['componentsAll'] = $compo = $this->pcmachieve->getMprComponentsall($filter);
        $this->getTableHeaders($data);
        $data['results'] = $results = $this->pcmachieve->showTargetAchData($filter);
//printr($compo); exit;
        $data['target_acv_data'] = array();

        foreach ($results as $result) {
            $arraysecond = array();
            foreach ($compo as $compos) {
                $targetKey = 'target' . $compos['id'];
                $uptoAchKey = 'upto_ach' . $compos['id'];
                $curAchKey = 'cur_ach' . $compos['id'];
                $totalAchKey = 'tota_ach' . $compos['id'];

                $arraysecond[$targetKey] = isset($result[$targetKey]) ? $result[$targetKey] : 0;
                $arraysecond[$uptoAchKey] = isset($result[$uptoAchKey]) ? $result[$uptoAchKey] : 0;
                $arraysecond[$curAchKey] = isset($result[$curAchKey]) ? $result[$curAchKey] : 0;
                $arraysecond[$totalAchKey] = $result[$curAchKey] + $result[$uptoAchKey];
            }

            $data['target_acv_data'][] = array(
                'district_id' => $result['district_id'],
                'district' => $result['district'],
                'total_block' => $result['total_block'],
                'arraysecond' => $arraysecond,
            );
        }
        //printr($data['target_acv_data']); exit;
        return $this->template->view('Admin\Physicalachievement\Views\componentachievedata', $data);
    }


    public function enterprisesindex()
    {

        $data = [];
        $user  = service('user');
        $data['year_id'] = getCurrentYearId();
        $data['allYears'] =  $this->years->withDeleted()->where('id >', 1)->findAll();
        $data['get_months'] = getMonths();
        //$data['monthId'] = $this->request->getGet('month_id');
        $currentMonth = date('n') - 1;
        $data['monthId'] =  $currentMonth;


        $data['getMonths'] = getMonthById(getMonthIdByMonth($currentMonth));
        if ($this->request->getGet('month_id')) {
            $data['getMonths'] = getMonthById(getMonthIdByMonth($this->request->getGet('month_id')));
        }
        // printr($getMonths['name']); exit;

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
        $data['selectedYear'] = 2;
        if ($this->request->getGet('year_id')) {
            $data['selectedYear'] = $this->request->getGet('year_id');
        }
        $data['checkExists'] = $this->pcmTarget->showCheckExistsData($filter);
        $data['componentsAll'] = $compo = $this->pcmachieve->getMprComponentsallEnt($filter);
        $this->getTableHeaders($data);
        $data['results'] = $results = $this->pcmachieve->showTargetAchDataEnt($filter);
     //   printr($results); exit;
        $data['target_acv_data'] = array();

        foreach ($results as $result) {
            $arraysecond = array();
            foreach ($compo as $compos) {
                $targetKey = 'target' . $compos['id'];
                $upto_fpoKey = 'upto_fpo' . $compos['id'];
                $upto_wshgKey = 'upto_wshg' . $compos['id'];
                $cur_fpoKey = 'cur_fpo' . $compos['id'];
                $cur_wshgKey = 'cur_wshg' . $compos['id'];

                $totalAchfpoKey = 'tota_achfpo' . $compos['id'];
                $totalAchwshgKey = 'tota_achwshg' . $compos['id'];

                $arraysecond[$targetKey] = isset($result[$targetKey]) ? $result[$targetKey] : 0;
                $arraysecond[$upto_fpoKey] = isset($result[$upto_fpoKey]) ? $result[$upto_fpoKey] : 0;
                $arraysecond[$upto_wshgKey] = isset($result[$upto_wshgKey]) ? $result[$upto_wshgKey] : 0;
                $arraysecond[$cur_fpoKey] = isset($result[$cur_fpoKey]) ? $result[$cur_fpoKey] : 0;
                $arraysecond[$cur_wshgKey] = isset($result[$cur_wshgKey]) ? $result[$cur_wshgKey] : 0;
                $arraysecond[$totalAchfpoKey] = $result[$upto_fpoKey] + $result[$cur_fpoKey];
                $arraysecond[$totalAchwshgKey] = $result[$upto_wshgKey] + $result[$cur_wshgKey];
            }

            $data['target_acv_data'][] = array(
                'district_id' => $result['district_id'],
                'district' => $result['district'],
                'total_block' => $result['total_block'],
                'arraysecond' => $arraysecond,
            );
        }
        //printr($data['target_acv_data']); exit;
        return $this->template->view('Admin\Physicalachievement\Views\componentachievedataent', $data);
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
            //  exit;
            $this->pcmachieve->addPhysicalachData($this->request->getPost());
            $this->session->setFlashdata('message', 'Physical Targets added Successfully.');

            return redirect()->to(base_url('admin/phyachtraining'));
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
        $data['editYear'] = '';
        $data['allYears'] =  $this->years->withDeleted()->where('id >', 1)->findAll();
        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            // $data['main_master'] =$components_info = $this->pcmTarget->getTargetcomponent($this->uri->getSegment(4));

        }
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
        $currentYearId = getCurrentYearId();

        $filter = [
            'year_id' => $yearId,
            'district_id' => $districtId,
            'month_id' => $monthId,

        ];
        $currentYearStartMonth = 4;
        $currentMonth = date('n');
        $currentYear = getCurrentYearId();

        $futureYear = $currentYear + 1;
        $nextYearAprilMonth = 4;
        //$currentYear = 3;
        $checkExistsmonthData = $this->pcmachieve->showGetExsistsMonthData($filter);
        // printr(!empty($checkExistsmonthData->month_id)) ; exit;

        // $day = date('j');
        // if ($day <= 1 || $day >= 5) {
        //     echo '<h3 style="text-align: center; margin-top: 20px;">Fill-up month date should be from 1 to 5.</h3>';
        //     exit;
        // }


        if ($yearId > $currentYear) {
            echo '<h3 style="text-align: center; margin-top: 20px;">Selected year is not allowed.</h3>';
            exit;
        }
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
        $components = $this->physicalcomponents->getComponents();
        $componentsData = $this->pcmachieve->getComponentsAllData($filter);
        //printr($componentsData); exit;
        $tableHeaderHtml = '<thead><tr>
                        <th style="text-align: center;">Components</th>
                        <th style="text-align: center;">Target for ' . $yearName . '</th>
                        <th style="text-align: center;">UP To This Month</th>
                        <th style="text-align: center;">' . $monthName . '</th>
                        <th  style="width: 10%; text-align: center;">Fpo</th>
                        <th  style="width: 10%; text-align: center;">Wshg</th>
                        <th style="text-align: center;width: 10%;">Cumulative</th>
                    </tr></thead>';
        $tableBodyHtml = '';
        foreach ($componentsData as $component) {
            $tableBodyHtml .= '<tr>';
            $tableBodyHtml .= '<td>' . $component['component'] . '</td>';
            $tableBodyHtml .= '<td class="targettotal">' . $component['targettotal'] . '</td>';
            $tableBodyHtml .= '<td class="achTotal">' . $component['ach_total'] . '</td>';
            $tableBodyHtml .= '<td><input type="number" name="component[' . $component['id'] . '][data]" class="form-control currentMonth" id="currentMonth" value="' . $component['cur_total'] . '"></td>';
            if ($component['comp_categoryid'] == 1) { // Check the category ID
                $tableBodyHtml .= '<td><input type="number" name="component[' . $component['id'] . '][fpo]" class="form-control fwsg fpo" id="fpo" value="' . $component['fpo_total'] . '"></td>';
                $tableBodyHtml .= '<td><input type="number" name="component[' . $component['id'] . '][wshg]" class="form-control fwsg wshg" id="wshg" value="' . $component['wshg_total'] . '"></td>';
            } else {
                $tableBodyHtml .= '<td></td><td></td>'; // Empty cells if not in category 2
            }

            $tableBodyHtml .= '<td><input type="text" name="" class="form-control cumulative" id="cumulative" readonly></td>';
            $tableBodyHtml .= '<td class="message"></td>';
            $tableBodyHtml .= '<input type="hidden" name="monthid" value="' . $monthId . '">';
            $tableBodyHtml .= '<input type="hidden" name="componentidmain" value="' . $checkExistsData->id . '">';
            $tableBodyHtml .= '</tr>';
        }
        $tableBodyHtml .= '<tr class="text-right"><td colspan="7"><button id="submitButton" class="btn btn-alt-primary">Submit</button></td></tr>';
        $tableBodyHtmlfinal = $tableHeaderHtml . '<tbody>' . $tableBodyHtml . '</tbody>';
        echo $tableBodyHtmlfinal;
    }
}
/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */