<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\BudgetModel;
use App\Controllers\AdminController;


class Budget extends AdminController
{
    private $budgetmodel;
    public function __construct()
    {
        $this->budgetmodel = new BudgetModel();
    }
    public function index()
    {
        return $this->getListForm();
    }
    private function getListForm()
    {
        $this->uri->setSilent();
        $this->template->add_package(array('datatable'), true);
        $data['datatable_url'] = admin_url('budgetcode/search');
        $id = $this->uri->getSegment(4);

        if ($id) {
            $data['action'] = admin_url("budgetcode/edit/{$id}");
        } else {
            $data['action'] = admin_url('budgetcode/add');
        }

        if ($id && ($this->request->getMethod(true) != 'POST')) {
            $budget_info = $this->budgetmodel->find($id);
            //printr($unit_info);
        }

        foreach ($this->budgetmodel->getFieldNames('enterprises_budget_codes') as $field) {
            if ($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if (isset($budget_info->{$field}) && $budget_info->{$field}) {
                $data[$field] = html_entity_decode($budget_info->{$field}, ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }

        $data['budget_code'] = $this->getForm($data);
        echo $this->template->view('Admin\Enterprises\Views\budgetCode', $data);
    }

    public function search()
    {
        $requestData = $_REQUEST;
        $totalData = $this->budgetmodel->getTotal();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'order'           => $requestData['order'][0]['dir'],
            'sort'              => $requestData['order'][0]['column'],
            'start'              => $requestData['start'],
            'limit'              => $requestData['length']
        );
        $totalFiltered = $this->budgetmodel->getTotal($filter_data);

        $filteredData = $this->budgetmodel->getAll($filter_data);

        $datatable = array();
        foreach ($filteredData as $key => $result) {

            $action  = '<div class="btn-group btn-group-sm pull-left">';
            $action .=         '<a class="btn btn-sm btn-primary ajaxaction" href="' . admin_url('budgetcode/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .=        '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('budgetcode/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[] = array(
                $key + 1,
                $result->budget_code,
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

            $id = $this->budgetmodel->insert($this->request->getPost());
            $this->session->setFlashdata('message', 'Budgetunit Code Saved Successfully.');

            return redirect()->to(admin_url('budgetcode'));
        }
        $this->getListForm();
    }

    public function edit()
    {

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $id = $this->uri->getSegment(4);

            $this->budgetmodel->update($id, $this->request->getPost());
            $this->session->setFlashdata('message', 'BudgetUnit Code Updated Successfully.');

            return redirect()->to(admin_url('budgetcode'));
        }
        $this->getListForm();
    }
    public function delete()
    {

        $id = $this->uri->getSegment(4);
        $this->budgetmodel->where('id', $id)->delete();
        return redirect()->to(admin_url('budgetcode'));
    }

    protected function validateForm()
    {

        $validation =  \Config\Services::validation();
        $id = $this->uri->getSegment(4);

        $rules = $this->budgetmodel->validationRules;

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

        return view('Admin\Enterprises\Views\budgetForm', $data);
    }
}
