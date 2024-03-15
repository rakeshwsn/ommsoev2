<?php

namespace Admin\Localisation\Controllers;

use App\Controllers\AdminController;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\ClusterModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;

class Block extends AdminController
{
    private $error = [];
    private $blockModel;

    public function __construct()
    {
        $this->blockModel = new BlockModel();
    }

    public function index()
    {
        $this->template->set_meta_title(lang('Block.heading_title'));
        return $this->getList();
    }

    public function add()
    {
        $this->template->set_meta_title(lang('Block.heading_title_add'));

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {
            $id = $this->blockModel->insert($this->request->getPost());
            $this->session->setFlashdata('message', lang('Block.message_add_success'));
            return redirect()->to(base_url('admin/block'));
        }

        $this->getForm();
    }

    public function edit()
    {
        $this->template->set_meta_title(lang('Block.heading_title_edit'));

        if ($this->request->getMethod() === 'post' && $this->validateForm()) {
            $id = $this->uri->getSegment(4);
            $this->blockModel->update($id, $this->request->getPost());
            $this->session->setFlashdata('message', lang('Block.message_edit_success'));
            return redirect()->to(base_url('admin/block'));
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

        $this->blockModel->delete($selected);
        $this->session->setFlashdata('message', lang('Block.message_delete_success'));
        return redirect()->to(base_url('admin/block'));
    }

    protected function getList()
    {
        $data['breadcrumbs'] = [
            [
                'text' => lang('Block.heading_title'),
                'href' => admin_url('block')
            ]
        ];

        $this->template->add_package(array('datatable', 'select2'), true);

        $data['add'] = admin_url('block/add');
        $data['delete'] = admin_url('block/delete');
        $data['datatable_url'] = admin_url('block/search');

        $data['heading_title'] = lang('Block.heading_title');

        $data['text_list'] = lang('Block.text_list');
        $data['text_no_results'] = lang('Block.text_no_results');
        $data['text_confirm'] = lang('Block.text_confirm');

        $data['button_add'] = lang('Block.button_add');
        $data['button_edit'] = lang('Block.button_edit');
        $data['button_delete'] = lang('Block.button_delete');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = [];
        }

        $districtModel = new DistrictModel();
        $data['districts'] = $districtModel->orderBy('name')->findAll();

        return $this->template->view('Admin\Localisation\Views\block', $data);
    }

    public function search()
    {
        $requestData = $this->request->getGet();
        $totalData = $this->blockModel->getTotals();
        $totalFiltered = $totalData;

        $filter_data = [
            'filter_search' => $requestData['search']['value'],
            'filter_district' => $requestData['district'],
            'filter_block' => $requestData['block'],
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        ];

        $totalFiltered = $this->blockModel->getTotals($filter_data);

        $filteredData = $this->blockModel->getAll($filter_data);

        $datatable = [];
        foreach ($filteredData as $result) {
            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('block/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('block/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[] = [
                '<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
                $result->name,
                $result->district,
                $action
            ];
        }

        $json_data = [
            'draw' => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($
