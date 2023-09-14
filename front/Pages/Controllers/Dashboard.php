<?php

namespace Front\Pages\Controllers;

use Api\Dashboard\Models\DistrictModel;
use Api\Dashboard\Models\OdMapModel;
use Api\Dashboard\Models\YearModel;
// use Admin\Pages\Models\PagesModel;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
	public function index() {
		
        $this->template->set_meta_title('OMM Dashboard');

		helper('form');

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
		$odmapmodel = new OdMapModel();
		$data['maps'] = $odmapmodel->getestablishmentmap();


		$data['area_url'] = site_url('api/areacoverage');
		$data['procure_url'] = site_url('api/procurement_c');
		$data['pds_url'] = site_url('api/pds_c');
		$data['establish_url'] = site_url('api/establishment');
		$data['enterprise_url'] = site_url('api/enterprises2');
		$data['odishamap_url'] = site_url('api/odishamap');
		
		$this->template->set_layout('default');
		$this->template->set('header',false);
		$this->template->set('home',false);

		return $this->template->view('Front\Pages\Views\dashboardchart', $data);
	}

	
	
}

//return  __NAMESPACE__ ."\Auth";