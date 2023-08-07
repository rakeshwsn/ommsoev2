<?php

namespace Admin\PhysicalComponents\Controllers;

use Admin\Permission\Models\PermissionModel;
use Admin\Physicalcomponents\Models\ComponentPhyModel;
use Admin\Components\Models\ComponentsModel;
use App\Controllers\AdminController;
use CodeIgniter\API\ResponseTrait;

class Physicalcomponents extends AdminController
{
    private $error = array();
    private $usergroupModel;
    private $enterprisesModel;
    use ResponseTrait;
    public function __construct()
    {
        $this->physicalcomponents = new ComponentPhyModel();
        $this->componentsModel = new ComponentsModel();
    }

    public function index()
    {
        $this->template->set_meta_title('Components');
        return $this->getList();
    }

    public function add()
    {
        $this->template->set_meta_title('Components');

        if ($this->request->getMethod(1) === 'POST') {
            // printr($this->request->getPost());
            // exit;
            $this->physicalcomponents->insertMprComponents($this->request->getPost());
            $this->session->setFlashdata('message', 'Physicalcomponents added Successfully.');

            return redirect()->to(base_url('admin/physicalcomponents'));
        }
        $this->getForm();
    }

    public function edit()
    {
        $this->template->set_meta_title('Components');

        if ($this->request->getMethod(1) === 'POST') {
            $id = $this->uri->getSegment(4);

            $this->physicalcomponents->updateComponentData($id, $this->request->getPost());
            $this->session->setFlashdata('message', 'Physicalcomponents Updated Successfully.');

            return redirect()->to(base_url('admin/physicalcomponents'));
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

    protected function getList()
    {

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'Phyisical Components',
            'href' => admin_url('physicalcomponents')
        );

        $this->template->add_package(array('datatable'), true);

        $data['add'] = admin_url('physicalcomponents/add');
        $data['delete'] = admin_url('physicalcomponents/delete');
        $data['datatable_url'] = admin_url('physicalcomponents/search');

        $data['heading_title'] = 'Physical components';

        $data['text_list'] = lang('Usergroup.text_list');
        $data['text_no_results'] = lang('Usergroup.text_no_results');
        $data['text_confirm'] = lang('Usergroup.text_confirm');

        $data['button_add'] = 'Physicalcomponents add';
        $data['button_edit'] = 'physicalcomponents edit';
        $data['button_delete'] = 'physicalcomponents delete';

        if (isset($this->error['warning'])) {
            $data['error']     = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        return $this->template->view('Admin\Physicalcomponents\Views\components', $data);
    }

    public function search()
    {
        $requestData = $_REQUEST;
        $totalData = $this->physicalcomponents->getTotal();
        $totalFiltered = $totalData;

        $filter_data = array(

            'filter_search' => $requestData['search']['value'],
            'order'           => $requestData['order'][0]['dir'],
            'sort'              => $requestData['order'][0]['column'],
            'start'              => $requestData['start'],
            'limit'              => $requestData['length']
        );
        $totalFiltered = $this->physicalcomponents->getTotal($filter_data);
        $filteredData = $this->physicalcomponents->getAll($filter_data);
       //printr($filteredData); exit;
        $datatable = array();
        foreach ($filteredData as $result) {

            $action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .=         '<a class="btn btn-sm btn-primary" href="' . admin_url('physicalcomponents/edit/' . $result['id']) . '"><i class="fa fa-pencil"></i></a>';
            $action .=        '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('physicalcomponents/delete/' . $result['id']) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';
            $year = '';
            if ($result['year_id'] == 2) {
                $year = '2023-24';
            } else if ($result['year_id'] == 3) {
                $year = '2024-25';
            }

            $datatable[] = array(
                '<input type="checkbox" name="selected[]" value="' . $result['id'] . '" />',
                $result['description'],
                $year,
                $action
            );
        }
        //printr($datatable);
        $json_data = array(
            "draw"            => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $datatable
        );

        return $this->response->setContentType('application/json')
            ->setJSON($json_data);
    }

    public function componentsearch()
    {
        $inputValue = $this->request->getGet('input_value');
        $componentsData = $this->physicalcomponents->getComponentsSearch($inputValue);
        return $this->respond($componentsData);
    }

    protected function getForm()
    {
        $this->template->add_package(array('select2'), true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'physical components',
            'href' => admin_url('enterprises')
        );


        $data['heading_title']     = 'physical components';
        $data['text_form'] = $this->uri->getSegment(4) ? "Physical Components Edit" : "Physical Components Add";
        $data['cancel'] = admin_url('physicalcomponents');
        //$data['componentsData'] = $this->componentsModel->getAll();
        $data['currentYearId'] = getCurrentYearId();
        if (isset($this->error['warning'])) {
            $data['error']     = $this->error['warning'];
        }
        $data['hasPlusbutton'] = true;
       // $data['components_info'] = '';
        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $data['components_info'] = $this->physicalcomponents->find($this->uri->getSegment(4));
           // printr($data['components_info']); exit;
            $data['hasPlusbutton'] = false;
        }
        //$data['components_info'] = new stdClass();
        //printr($data['permissions']);
        foreach ($this->physicalcomponents->getFieldNames('mpr_components') as $field) {
            if ($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if (isset($components_info->{$field}) && $components_info->{$field}) {
                $data[$field] = html_entity_decode($components_info->{$field}, ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }
        echo $this->template->view('Admin\Physicalcomponents\Views\componentsForm', $data);
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