<?php

namespace Admin\Dashboard\Controllers;

use Admin\OdishaMap\Models\OdishaMapModel;
use Admin\Dashboard\Controllers\Api;
use Api\Dashboard\Models\YearModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\CurrentYearChartModel;
use App\Controllers\AdminController;


class Currentyear extends AdminController
{
    public function index()
    {
		$yearModel = new YearModel();
		$districtmodel = new DistrictModel();
		$currentyeardata = new CurrentYearChartModel();
		$data['year_id'] = $filter['year_id'] = $this->request->getGet('year_id') ?? ((new YearModel())->getCurrentYear())->id;
	
		$crntyrdatas = $currentyeardata->getcurrentyeardata($filter);
	
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
