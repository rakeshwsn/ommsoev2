<?php

namespace Admin\FPO\Controllers;

use Admin\FPO\Models\FPOModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use CodeIgniter\Images\Image;
use Config\Url;

class FPO extends AdminController
{
    use TreeTrait;

    private $fpoModel;
    private $districtModel;
    private $blockModel;

    public function __construct()
    {
        $this->fpoModel = new FPOModel();
        $this->districtModel = new DistrictModel();
        $this->blockModel = new BlockModel();
        helper('fpo');
    }

    public function index()
    {
        $data['fpo_districts'] = $this->fpoModel->getFPOByDistrict();

        $districtId = $this->user->district_id ?? null;
        $districts = $districtId ? $this->districtModel->getAll(['filter_district' => $districtId]) : $this->districtModel->getAll();

        foreach ($districts as $district) {
            $district->fpos = $this->fpoModel->getFPOByBlock($district->id);
        }

        $data['district_id'] = $this->user->district_id;
        $data['districts'] = $districts;
        $data['fpo_status'] = $this->fpoModel->getFPOStatus();
        $data['register_status'] = ['1' => 'Preliminary work  Under Process', '2' => 'Applied for FPO'];

        return $this->template->view('Admin\FPO\Views\index', $data);
    }

    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $json_data = [
            'status' => false,
        ];

        if ($this->request->getMethod() === 'POST') {
            $this->validate([
                'district_id' => 'required|integer',
                'block_id' => 'required|integer',
            ]);

            if (!$this->validator->hasError('district_id') && !$this->validator->hasError('block_id')) {
                $this->fpoModel->insert([
                    'district_id' => $this->request->getPost('district_id'),
                    'block_id' => $this->request->getPost('block_id'),
                    'registered' => $this->request->getPost('registered'),
                    'register_status' => $this->request->getPost('register_status'),
                    'other_fpo' => $this->request->getPost('other_fpo'),
                    'other_block_id' => $this->request->getPost('other_block_id'),
                    'name' => $this->request->getPost('name'),
                    'act' => $this->request->getPost('act'),
                ]);

                $json_data = [
                    'status' => true,
                ];
                $this->session->setFlashData('message', 'FPO added.');
            }
        } else {
            $json_data = [
                'status' => true,
                'title' => 'Add FPO',
                'html' => $this->getForm(),
            ];
        }

        return $this->response->setJSON($json_data);
    }

    public function edit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $json_data = [
            'status' => false,
        ];

        if ($this->request->getMethod() === 'POST') {
            $id = $this->uri->getSegment(4);

            $this->validate([
                'district_id' => 'required|integer',
                'block_id' => 'required|integer',
            ]);

            if (!$this->validator->hasError('district_id') && !$this->validator->hasError('block_id')) {
                $this->fpoModel->update($id, [
                    'district_id' => $this->request->getPost('district_id'),
                    'block_id' => $this->request->getPost('block_id'),
                    'registered' => $this->request->getPost('registered'),
                    'register_status' => $this->request->getPost('register_status'),
                    'other_fpo' => $this->request->getPost('other_fpo'),
                    'other_block_id' => $this->request->getPost('other_block_id'),
                    'name' => $this->request->getPost('name'),
                    'act' => $this->request->getPost('act'),
                ]);

                $json_data = [
                    'status' => true,
                ];

                $this->session->setFlashdata('message', 'FPO Updated Successfully');
            }
        } else {
            $json_data = [
                'status' => true,
                'title' => 'Edit FPO',
                'html' => $this->getForm(),
            ];
        }

        return $this->response->setJSON($json_data);
    }

    public function details()
    {

