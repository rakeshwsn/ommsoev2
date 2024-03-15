<?php

namespace App\Controllers\Admin\Users;

use App\Controllers\AdminController;
use App\Models\Admin\Users\UserGroupModel;
use App\Models\Admin\Permission\PermissionModel;

class Usergroup extends AdminController
{
    private $usergroupModel;
    private $permissionModel;
    private $data;

    public function __construct()
    {
        parent::__construct();
        $this->usergroupModel = new UserGroupModel();
        $this->permissionModel = new PermissionModel();
    }

    public function index()
    {
        $this->data['title'] = lang('Users.heading_title');
        return $this->getList();
    }

    protected function getList()
    {
        $this->data['breadcrumbs'] = [
            [
                'text' => lang('Usergroup.heading_title'),
                'href' => admin_url('usergroup')
            ],
        ];

        $this->data['add'] = admin_url('usergroup/add');
        $this->data['delete'] = admin_url('usergroup/delete');
        $this->data['datatable_url'] = admin_url('usergroup/search');

        $this->data['heading_title'] = lang('Usergroup.heading_title');
        $this->data['text_list'] = lang('Usergroup.text_list');
        $this->data['text_no_results'] = lang('Usergroup.text_no_results');
        $this->data['text_confirm'] = lang('Usergroup.text_confirm');
        $this->data['button_add'] = lang('Usergroup.button_add');
        $this->data['button_edit'] = lang('Usergroup.button_edit');
        $this->data['button_delete'] = lang('Usergroup.button_delete');

        if (isset($this->error['warning'])) {
            $this->data['error'] = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $this->data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $this->data['selected'] = array();
        }

        return $this->template->view('Admin\Users\Views\userGroup', $this->data);
    }

    public function add()
    {
        $this->data['title'] = lang('Usergroup.heading_title');

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $this->usergroupModel->insert($this->request->getPost());
            $this->session->setFlashdata('message', 'Member Saved Successfully.');

            return redirect()->to(base_url('admin/usergroup'));
        }

        $this->getForm();
    }

    protected function validateForm()
    {
        $validation = \Config\Services::validation();
        $rules = $this->usergroupModel->validationRules;

        if ($this->validate($rules)) {
            return true;
        } else {
            $this->error['warning'] = "Warning: Please check the form carefully for errors!";
            return false;
        }
    }

    protected function getForm()
    {
        $this->data['breadcrumbs'] = [
            [
                'text' => lang('Usergroup.heading_title'),
                'href' => admin_url('usergroup')
            ],
        ];

        $this->data['heading_title'] = lang('Usergroup.heading_title');
        $this->data['text_form'] = $this->uri->getSegment(4) ? "Usergroup Edit" : "Usergroup Add";
        $this->data['cancel'] = admin_url('usergroup');

        if (isset($this->error['warning'])) {
            $this->data['error'] = $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $usergroup_info = $this->usergroupModel->find($this->uri->getSegment(4));
            $this->data['usergroup_info'] = $usergroup_info;
        }

        if ($this->request->getPost()) {
            $this->data['usergroup'] = $this->request->getPost();
        }

        $this->template->add_package(array('select2'), true);

        return $this->template->view('Admin\Users\Views\userGroupForm', $this->data);
    }

    public function edit()
    {
        $this->data['title'] = lang('Users.heading_title');

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $id = $this->uri->getSegment(4);
            $this->usergroupModel->update($id, $this->request->getPost());
            $this->session->setFlashdata('message', 'Member Updated Successfully.');

            return redirect()->to(base_url('admin/usergroup'));
        }

        $this->getForm();
    }

    public function delete()
    {
        if ($this->request->getPost('selected')) {
            $selected = $this->request->getPost('selected');
        } else {
            $selected = (array)$this->uri->getSegment(4);
        }
        $this->usergroupModel->delete($selected);
        $this->session->setFlashdata('message', 'Usergroup deleted Successfully.');
        return redirect()->to(base_url('admin/usergroup'));
    }

    public function search()
    {
        // ... (same as the original code)
    }

    public function permission()
    {
        // ... (same as the original code)
    }

    protected function get_access_methods($class)
    {
        // ... (same as the original code)
    }
}
