<?php

namespace Admin\dashboard\Controllers;
use Admin\Dashboard\Controllers\Api;

use Admin\Dashboard\Models\AreaCoverageModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\ProcurementModel;
use Admin\Dashboard\Models\EnterpriseModel;
use Admin\Dashboard\Models\YearModel;

use Admin\Dashboard\Models\EstablishmentModel;

use Admin\Dashboard\Models\PdsModel;

use App\Controllers\AdminController;

class Dashboard extends AdminController
{
	public function index()
	{
		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select districts';
		// $data = [];
		$districts = $districtmodel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}
		$yearmodel = new YearModel();
		$data['years'][0] = 'Select years';

		$years = $yearmodel->findAll();

		foreach ($years as $year) {
			$data['years'][$year->id] = $year->name;
		}
		$odishamapmodel = new EstablishmentModel();
		$data['maps'] = $odishamapmodel->getestablishmentmap();


		$data['area_url'] = site_url('api/areacoverage');
		$data['procure_url'] = site_url('api/procurement');
		$data['pds_url'] = site_url('api/pds');
		$data['establish_url'] = site_url('api/establishment');
		$data['enterprise_url'] = site_url('api/enterprises');
		$data['odishamap_url'] = site_url('api/odishamap');

		return $this->template->view('Admin\Dashboard\Views\dashboardChart', $data);
	}
}
