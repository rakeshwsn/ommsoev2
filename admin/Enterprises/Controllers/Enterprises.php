<?php

namespace Admin\Enterprises\Controllers;

use Admin\Permission\Models\PermissionModel;
use Admin\Users\Models\UserGroupModel;
use App\Controllers\AdminController;
use CodeIgniter\DebugToolbar\Services\DebugToolbar;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\URI;
use CodeIgniter\Session\Session;
use CodeIgniter\Validation\Validation;

class Enterprises extends AdminController
{
    private UserGroupModel $usergroupModel;
    private PermissionModel $permissionModel;
    private Validation $validation;
    private Session $session;
    private DebugToolbar $debugToolbar;
    private array $validationRules;
    private array $validationMessages;
    private array $validationErrors;
    private array $modules;
    private array $controllers;
    private array $permissions;
    private array $access;

    public function __construct()
    {
        parent::__construct();

        $this->usergroupModel = new UserGroupModel();
        $this->permissionModel = new PermissionModel();
        $this->validation = service('validation');
        $this->session = service('session');
        $this->debugToolbar = service('debugtoolbar');
        $this->validationRules = $this->usergroupModel->validationRules;
        $this->validationMessages = $this->usergroupModel->validationMessages;
    }

    public function index(): string
    {
        $this->template->set_meta_title('enterprises');

        return $this->getList();
    }

    public function add(): string
    {
        $this->template->set_meta_title('enterprises');

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $this->usergroupModel->insert($this->request->getVar());
            $this->session->setFlashdata('message', 'Member Saved Successfully.');

            return redirect()->to(base_url('admin/usergroup'));
        }

        return $this->getForm();
    }

    public function edit(): string
    {
        $this->template->set_meta_title(lang('Users.heading_title'));

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()) {
            $id = $this->request->uri->getSegment(4);

            $this->usergroupModel->update($id, $this->request->getVar());
            $this->session->setFlashdata('message', 'Member Updated Successfully.');

            return redirect()->to(base_url('admin/usergroup'));
        }

        return $this->getForm();
    }

    public function delete(): ResponseInterface
    {
        if ($this->request->getPost('selected')) {
            $selected = $this->request->getPost('selected');
        } else {
            $selected = (array) $this->request->uri->getSegment(4);
        }

        $this->usergroupModel->delete($selected);
        $this->session->setFlashdata('message', 'Usergroup deleted Successfully.');

        return redirect()->to(base_url('admin/usergroup'));
    }

    protected function getList(): string
    {
        $data['breadcrumbs'] = [
            [
                'text' => lang('Usergroup.heading_title'),
                'href' => admin_url('usergroup')
            ],
        ];

        $this->template->add_package(array('datatable'), true);

        $data['add'] = admin_url('usergroup/add');
        $data['delete'] = admin_url('
