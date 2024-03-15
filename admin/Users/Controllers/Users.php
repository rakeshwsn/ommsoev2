<?php

namespace App\Controllers\Admin\Users;

use App\Controllers\AdminController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Validation\ValidationInterface;
use Config\Services;

class Users extends AdminController
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var array
     */
    private $error = [];

    /**
     * Users constructor.
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, ValidationInterface $validation)
    {
        parent::__construct($request, $response, $validation);

        $this->userModel = new UserModel();
    }

    /**
     * @return string
     */
    public function index(): string
    {
        $this->template->setMetaTitle(lang('Users.heading_title'));

        return $this->getList();
    }

    /**
     * @return string
     */
    public function add(): string
    {
        $this->template->setMetaTitle(lang('Users.heading_title'));

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {
            $userid = $this->userModel->insert($this->request->getVar());

            $this->session->setFlashdata('message', 'User Saved Successfully.');

            return $this->response->redirect(site_url('admin/users'));
        }

        return $this->getForm();
    }

    /**
     * @return string
     */
    public function edit(): string
    {
        $this->template->setMetaTitle(lang('Users.heading_title'));

        $id = $this->request->getUri()->getSegment(4);

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {
            $this->userModel->update($id, $this->request->getVar());

            $this->session->setFlashdata('message', 'User Updated Successfully.');

            return $this->response->redirect(site_url('admin/users'));
        }

        return $this->getForm();
    }

    /**
     * @return string
     */
    public function delete(): string
    {
        if ($this->request->getVar('selected')) {
            $selected = $this->request->getVar('selected');
        } else {
            $selected = (array) $this->request->getUri()->getSegment(4);
        }

        $this->userModel->delete($selected);

        foreach ($selected as $id) {
            //$this->userModel->deleteCentralUser($user->central_appuser_id);
        }

        $this->session->setFlashdata('message', 'User deleted Successfully.');

        return $this->response->redirect(site_url('admin/users'));
    }

    /**
     * @return string
     */
    protected function getList(): string
    {
        $data['breadcrumbs'] = [
            [
                'text' => lang('Users.heading_title'),
                'href' => site_url('admin/users')
            ]
        ];

        $this->template->addPackage(array('datatable'), true);

        $data['add'] = site_url('admin/users/add');
        $data['delete'] = site_url('admin/users/delete');

