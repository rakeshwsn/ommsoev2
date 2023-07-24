<?php

namespace Admin\dashboard\Controllers;

use Admin\Dashboard\Models\AreaCoverageModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Dashboard\Models\ProcurementModel;
use Admin\Dashboard\Models\EnterpriseModel;
use Admin\Dashboard\Models\YearModel;

use Admin\Dashboard\Models\EstablishmentModel;

use Admin\Dashboard\Models\PdsModel;

use App\Controllers\AdminController;

class Api extends AdminController
{

	public function areacoverage()
	{
		$districtmodel = new DistrictModel();
		$areamodel = new AreaCoverageModel();
		$filter = [];
		$district_id = 0;
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');

			$filter['district_id'] = $this->request->getGet('district_id');
			$district_id =  $this->request->getGet('district_id');
		}

		$achievements = $areamodel->getYearwiseFarmerAchievement($filter);

		$data['areayears'] = [];
		$data['areafarmers'] = [];
		$data['areaachievements'] = [];

		foreach ($achievements as $achievement) {
			$data['areayears'][] = $achievement->year;
			$data['areafarmers'][] = $achievement->total_farmers;
			$data['areaachievements'][] = $achievement->total_ach;
		}
       
		$data['heading'] = 'AREA COVERAGE CHART';
		if ($district_id) {
			$data['heading'] .= ' FOR:-' . $districtmodel->find($district_id)->name;
		}

		header('Content-Type: application/json');
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
	public function procurement()
	{
		$districtmodel = new DistrictModel();

		$procuremodel = new ProcurementModel();
		$filter = [];
		$district_id = 0;

		if ($this->request->getGet('district_id') && $this->request->getGet('district_id') > 0) {
			$data['district_id'] = $this->request->getGet('district_id');

			$filter['district_id'] = $this->request->getGet('district_id');
			$district_id =  $this->request->getGet('district_id');
		}

		$procures = $procuremodel->getYearwisedata($filter);

		$data['pyears'] = [];
		$data['pfarmers'] = [];
		$data['pquantity'] = [];
		$data['ptotal_amount'] = [];

		foreach ($procures as $procure) {
			$data['pyears'][] = $procure->year;
			$data['pfarmers'][] = $procure->total_farmers;
			$data['pquantity'][] = $procure->total_quantity;
			$data['ptotal_amount'][] = $procure->total_amount;
		}
		$data['heading'] = 'PROCUREMENT';
		if ($district_id) {
			$data['heading'] .= ' FOR:-' . $districtmodel->find($district_id)->name;
		}

		header('Content-Type: application/json');
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
	public function pds()
	{

		$pdsmodel = new PdsModel();		
		$pdses = $pdsmodel->getYearwisepds();

		$data['pdsyear'] = [];
		$data['pdsquantity'] = [];
		$data['card_holders_benifited'] = [];

		foreach ($pdses as $pds) {
			$data['pdsyear'][] = $pds->year;
			$data['pdsquantity'][] = $pds->total_quantity;
			$data['card_holders_benifited'][] = $pds->total_chb;
		}
		

		header('Content-Type: application/json');
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
	public function establishment()
	{
		$establishmentchartmodel = new EstablishmentModel();
		$filter = [];
		$establishes = $establishmentchartmodel->getestablishment($filter);

		$data['estdistrict'] = [];
		$data['chc'] = [];
		$data['cmsc'] = [];
		$data['blocks'] = [];

		foreach ($establishes as $establish) {
			$data['estdistrict'][] = $establish->district;
			$data['chc'][] = $establish->chc;
			$data['cmsc'][] = $establish->cmsc;
			$data['blocks'][] = $establish->blocks;
		}
		// dd($data);
		header('Content-Type: application/json');
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
	public function enterprise()
	{
		$yearmodel = new YearModel();
		$districtmodel = new DistrictModel();

		$enterprisemodel = new EnterpriseModel();
		$filter = [];
		$year_id = $district_id = 0;
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
			$filter['district_id'] = $this->request->getGet('district_id');
			$district_id = $this->request->getGet('district_id');
		}
		if ($this->request->getGet('year_id')) {
			$data['year_id'] = $this->request->getGet('year_id');

			$filter['year_id'] = $this->request->getGet('year_id');
			$year_id = $this->request->getGet('year_id');
		}

		$enterprises = $enterprisemodel->getYearwisedata($filter);
		$data['enterprises'] = $enterprises;
		$data['year'] = [];
		$data['district'] = [];
		$data['unit_name'] = [];
		$data['wshg'] = [];
		$data['fpos'] = [];
		foreach ($enterprises as $enterprise) {
			$data['year'][] = $enterprise->year;
			$data['district'][] = $enterprise->district;
			$data['unit_name'][] = $enterprise->unit_name;
			$data['wshg'][] = $enterprise->total_wshg;
			$data['fpos'][] = $enterprise->total_fpos;
		}

		//heading
		$data['heading'] = 'Enterprise Chart';
		if ($year_id) {
			$data['heading'] .= ' for year ' . $yearmodel->find($year_id)->name;
		}

		if ($district_id) {
			$data['heading'] .= ' for district ' . $districtmodel->find($district_id)->name;
		}
	
		//generate table
		$data['table'] = view('Admin\Dashboard\Views\enterpriseTable', $data);

		header('Content-Type: application/json');
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
}
