<?php

namespace Admin\Dashboard\Controllers;

use Admin\OdishaMap\Models\OdishaMapModel;
use Admin\Dashboard\Controllers\Api;

use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\AreaCoverageModel;
use Admin\Dashboard\Models\BlockModel;
use Admin\Dashboard\Models\GpsModel;
use App\Controllers\AdminController;
use Admin\Dashboard\Models\YearModel;

class AreaCoverage extends AdminController
{
    public function index()
    {
        $areamodel = new AreaCoverageModel();
        $this->template->set_meta_title(lang('Pages.heading_title'));

        helper('form');

        $yearmodel = new YearModel();
        $data['years'][0] = 'Select years';

        $years = $yearmodel->findAll();

        foreach ($years as $year) {
            $data['years'][$year->id] = $year->name;
        }
        $distModel = new DistrictModel();
        $data['districts'][0] = 'Select Districts';

        $districts = $distModel->findAll();

        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $blockmodel = new BlockModel();
        $data['blocks'][0] = 'Select Blocks';

        $data['year_id'] = 0;
        $data['district_id'] = 0;
        $data['block_id'] = 0;
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');

            $blocks = $blockmodel->where('district_id', $data['district_id'])->findAll();

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        }
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }

        $filter = [];

        if ($data['year_id'] > 0) {
            $filter = [
                'year_id' => $data['year_id'],
            ];
        }

        if ($data['district_id'] > 0) {
            $filter['district_id'] = $data['district_id'];
        }

        if ($data['block_id'] > 0) {
            $filter['block_id'] = $data['block_id'];
        }
        $areaList = $areamodel->getAll($filter);

        $data['areas'] = [];

        foreach ($areaList as $row) {
            $data['areas'][] = [
                'year' => $row->year,
                'season' => $row->season,
                'districts' => $row->districts,
                'blocks' => $row->blocks,
                'farmers' => $row->farmers,
                'achievement' => $row->achievement,
                'created_at' => ymdToDmy($row->created_at),
                'edit_url' => admin_url('dashboard/areacoverage/edit?year_id=' . $row->year_id . '&district_id=' . $row->district_id . '&block_id=' . $row->block_id . '&season=' . $row->season),

            ];
        }

        return $this->template->view('Admin\Dashboard\Views\areacoverage', $data);
    }

    public function add()
    {
        $areamodel = new AreaCoverageModel();
        if ($this->request->getMethod(1) == 'POST') {

            //delete if year_id exists
            $areamodel->where('year_id', $this->request->getGet('year_id'))
                ->where('season', $this->request->getGet('season'))
                ->where('gp_id', $this->request->getGet('gp_id'))->delete();


            foreach ($this->request->getPost('gp') as $key => $values) {
                $areadata[] = [
                    'year_id' => $this->request->getGet('year_id'),
                    'gp_id' => $key,
                    'district_id' => $this->request->getGet('district_id'),
                    'farmers' => $values['farmers'],
                    'block_id' => $this->request->getGet('block_id'),
                    'season' => $this->request->getGet('season'),
                    'achievement' => $values['achievement'],
                ];

            }
            $areamodel->insertBatch($areadata);

            return redirect()->to(admin_url('dashboard/areacoverage'))->with('message', 'successful');
        }

        return $this->getForm();
    }

    public function edit()
    {
        $areamodel = new AreaCoverageModel();
        if ($this->request->getMethod(1) == 'POST') {

            $year_id = $this->request->getGet('year_id');
            $district_id = $this->request->getGet('year_id');
            $block_id = $this->request->getGet('block_id');
            $season = $this->request->getGet('season');

            $areamodel->where('year_id', $this->request->getGet('year_id'))
                ->where('season', $this->request->getGet('season'))
                ->where('gp_id', $this->request->getGet('gp_id'))
                ->where('district_id', $this->request->getGet('district_id'))->delete();

            foreach ($this->request->getPost('gp') as $key => $values) {
                $areadata[] = [
                    'year_id' => $year_id,
                    'gp_id' => $key,
                    'district_id' => $district_id,
                    'block_id' => $block_id,
                    'farmers' => $values['farmers'],
                    'season' => $season,
                    'achievement' => $values['achievement'],
                ];
            }
            $data['areas'] = $areamodel->insertBatch($areadata);

            return redirect()->to(admin_url('dashboard/areacoverage'))->with('message', 'successful');
        }


        return $this->getForm();
    }

    public function ajaxBlocks()
    {

        $data['blocks'] = [];
        $BlocksModel = new BlockModel();

        $district_id = $this->request->getGet('district_id');

        $data['blocks'] = $BlocksModel->where('district_id', $district_id)->findAll();

        return $this->response->setJSON($data);
    }

    private function getForm()
    {
        $data = [];
        helper('form');
        $areamodel = new AreaCoverageModel();
        $yearmodel = new YearModel();
        
        $data['years'][0] = 'Select years';

        $years = $yearmodel->findAll();

        foreach ($years as $year) {
            $data['years'][$year->id] = $year->name;
        }

        $distModel = new DistrictModel();
        $data['districts'][0] = 'Select Districts';

        $districts = $distModel->findAll();

        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $blockmodel = new BlockModel();
        $data['blocks'][0] = 'Select Blocks';

        $data['year_id'] = 0;
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }
        $data['district_id'] = 0;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');

            $blocks = $blockmodel->where('district_id', $data['district_id'])->findAll();

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        }
        $data['block_id'] = 0;
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }
        $data['season'] = '';
        if ($this->request->getGet('season')) {
            $data['season'] = $this->request->getGet('season');
        }

        $data['gps'] = [];
        $filter = [
            'block_id' => $data['block_id'],
            'year_id' => $data['year_id'],
            'season' => $data['season'],
        ];

        $gps = $areamodel->getByBlock($filter);
        foreach ($gps as $gp) {
            $data['gps'][] = [
                'id' => $gp->gp_id,
                'name' => $gp->gp,
                'farmers' => $gp->farmers ?: 0,
                'achievement' => $gp->achievement ?: 0,
            ];
        }

        $data['seasons'] = [
            'rabi' => 'Rabi',
            'kharif' => 'Kharif'
        ];

        return $this->template->view('Admin\Dashboard\Views\areacoverageForm', $data);
    }

    public function chart()
    {
        $this->template->set_meta_title(lang('Pages.heading_title'));

        $districtmodel = new DistrictModel();
        $data['districts'][0] = 'Select districts';

        $districts = $districtmodel->findAll();

        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }
        $data['area_url'] = site_url('api/areacoverage');
        return $this->template->view('Admin\Dashboard\Views\areaCoverageChart', $data);
    }
}
