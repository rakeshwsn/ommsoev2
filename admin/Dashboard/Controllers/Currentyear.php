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

class Currentyear extends AdminController
{
    public function index()
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
