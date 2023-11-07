<?php

namespace Admin\Dashboard\Controllers;

use Admin\OdishaMap\Models\OdishaMapModel;
use Admin\Dashboard\Controllers\Api;

use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\CurrentYearChartModel;
use App\Controllers\AdminController;


class Currentyear extends AdminController
{
    public function index()
    {
       
		$districtmodel = new DistrictModel();
		$currentyeardata = new CurrentYearChartModel();

		$crntyrdatas = $currentyeardata->getcurrentyeardata();
// dd($crntyrdatas);
		$data['currentdistrict'] = [];
		$data['currentfarmers'] = [];
		$data['currentachievements'] = [];

		foreach ($crntyrdatas as $crntyrdata) {
			$data['currentdistrict'][] = $crntyrdata->districts;
			$data['currentfarmers'][] = (int)$crntyrdata->total_farmers;
			$data['currentachievements'][] = (float)$crntyrdata->achievement;
		}
	// dd($data);	// return $this->respond($data);
        return $this->template->view('Admin\Dashboard\Views\currentyearchart', $data);
    }
}
