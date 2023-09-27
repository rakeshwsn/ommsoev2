<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\EnterprisesUnitmodel;
use App\Controllers\AdminController;


class EnterpriseUnit extends AdminController
{
    private $enterprisesunitmodel;
    public function __construct()
    {
        $this->enterprisesunitmodel = new EnterprisesUnitmodel();
    }
    public function index()
    {
        return $this->getListForm();
    }
    private function getListForm()
    {
        $this->uri->setSilent();
        $this->template->add_package(array('datatable'), true);
        $data['datatable_url'] = admin_url('enterpriseunit/search');
        $id = $this->uri->getSegment(4);

        if ($id) {
            $data['action'] = admin_url("enterpriseunit/edit/{$id}");
        } else {
            $data['action'] = admin_url('enterpriseunit/add');
        }

        if ($id && ($this->request->getMethod(true) != 'POST')) {
            $unit_info = $this->enterprisesunitmodel->find($id);
            //printr($unit_info);
        }

        foreach ($this->enterprisesunitmodel->getFieldNames('enterprises_units') as $field) {
            if ($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if (isset($unit_info->{$field}) && $unit_info->{$field}) {
                $data[$field] = html_entity_decode($unit_info->{$field}, ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }

        $data['form'] = $this->getForm($data);
        echo $this->template->view('Admin\Enterprises\Views\enterpriseUnit', $data);
    }

    public function search()
    {
        $requestData = $_REQUEST;
        $totalData = $this->enterprisesunitmodel->getTotal();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'order'           => $requestData['order'][0]['dir'],
            'sort'              => $requestData['order'][0]['column'],
            'start'              => $requestData['start'],
            'limit'              => $requestData['length']
        );
        $totalFiltered = $this->enterprisesunitmodel->getTotal($filter_data);

        $filteredData = $this->enterprisesunitmodel->getAll($filter_data);
    //    printr($filteredData); exit;
        $datatable = array();
        $startIndex = isset($requestData['start']) ? intval($requestData['start']) : 0; // Start index of the current page
        foreach ($filteredData as $key => $result) {

            $action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .=         '<a class="btn btn-sm btn-primary ajaxaction" href="' . admin_url('enterpriseunit/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            
            $action .=        '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('enterpriseunit/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';
            $sequentialNumber = $startIndex + $key + 1;
            $datatable[] = array(
                $sequentialNumber,
                $result->name,
                $result->group_unit,
                $result->total_ent,
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

    public function add()
    {

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {

            $id = $this->enterprisesunitmodel->insert($this->request->getPost());
            $this->session->setFlashdata('message', 'EnterprisesUnit Saved Successfully.');

            return redirect()->to(admin_url('enterpriseunit'));
        }
        $this->getListForm();
    }

    public function edit()
    {

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $id = $this->uri->getSegment(4);

            $this->enterprisesunitmodel->update($id, $this->request->getPost());
            $this->session->setFlashdata('message', 'Component Updated Successfully.');

            return redirect()->to(admin_url('enterpriseunit'));
        }
        $this->getListForm();
    }
    public function delete()
    {

        $id = $this->uri->getSegment(4);
        $this->enterprisesunitmodel->where('id', $id)->delete();
        return redirect()->to(admin_url('enterpriseunit'));
    }

    protected function validateForm()
    {

        $validation =  \Config\Services::validation();
        $id = $this->uri->getSegment(4);

        $rules = $this->enterprisesunitmodel->validationRules;

        if ($this->validate($rules)) {
            return true;
        } else {
            //printr($validation->getErrors());
            $this->error['warning'] = "Warning: Please check the form carefully for errors!";
            return false;
        }
        return !$this->error;
    }

    public function getForm($data)
    {

        return view('Admin\Enterprises\Views\enterprisesForm', $data);
    }
}
