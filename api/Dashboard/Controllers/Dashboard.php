<?php

namespace Api\Dashboard\Controllers;

use Api\Dashboard\Models\YearModel;
use Api\Dashboard\Models\AreaChartModel;
use Api\Dashboard\Models\DistrictMapModel;
use Api\Dashboard\Models\EstablishmentChartModel;
use Api\Dashboard\Models\PdsChartModel;
use Api\Dashboard\Models\ProcurementChartModel;
use Api\Dashboard\Models\EnterpriseChartModel;
use Api\Dashboard\Models\DistrictModel;
use Api\Dashboard\Models\OdMapModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Dashboard extends ResourceController
{
	use ResponseTrait;
	private $apiModel;
	private $user;

	public function __construct()
	{
		helper("aio");
		$this->user = service('user');
	}

	public function areacoverage(){ 
		
		$districtmodel = new DistrictModel();
		$areamodel = new AreaChartModel();
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
		return $this->respond(json_encode($data,JSON_NUMERIC_CHECK));
	}
	
	public function procurement(){
		
		$districtmodel = new DistrictModel();

		$procuremodel = new ProcurementChartModel();
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

		return $this->respond(json_encode($data,JSON_NUMERIC_CHECK));

	}
	
	public function pds(){

		$pdsmodel = new PdsChartModel();
		$pdses = $pdsmodel->getYearwisepds();

		$data['pdsyear'] = [];
		$data['pdsquantity'] = [];
		$data['card_holders_benifited'] = [];

		foreach ($pdses as $pds) {
			$data['pdsyear'][] = $pds->year;
			$data['pdsquantity'][] = $pds->total_quantity;
			$data['card_holders_benifited'][] = $pds->total_chb;
		}


		return $this->respond(json_encode($data,JSON_NUMERIC_CHECK));
	}
	
	public function establishment(){
		$establishmentchartmodel = new EstablishmentChartModel();
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
		return $this->respond(json_encode($data,JSON_NUMERIC_CHECK));
	}
	
	public function enterprise(){
		$yearmodel = new YearModel();
		$districtmodel = new DistrictModel();

		$enterprisemodel = new EnterpriseChartModel();;
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
		$data['table'] = view('Api\Dashboard\Views\dashboardTable', $data);

		return $this->respond(json_encode($data,JSON_NUMERIC_CHECK));
	}




	public function districtarea(){
		$districtmodel = new DistrictModel();
		$areamodel = new AreaChartModel();
		$yearmodel = new YearModel();
		$filter = [];
		if ($this->request->getGet('year_id')) {
			$filter['year_id'] = $this->request->getGet('year_id');
		}else{
			$filter['year_id']=((int)$yearmodel->getCurrentYearId()-1);
		}
		
		$data['heading'] = 'Area Vs No. Of Farmer';
		$areas = $areamodel->getFarmerAreaByDistrict($filter);
		$data['data']=[];
		foreach ($areas as $area) {
			$data['data'][]=[
				'year_id'=>(int)$area->year_id,
				'year'=>$area->year,
				'district_id'=>(int)$area->district_id,
				'district'=>$area->district,
				'total_farmers'=>(int)$area->total_farmer,
//				'intercropping'=>(float)$area->total_intercropping,
				'practice_area'=>(float)$area->total_area,
				'total_area'=>(float)$area->total_area,
			];
		}

		return $this->respond($data);
	}

	public function pds2(){

		$pdsmodel = new PdsChartModel();
		$pdses = $pdsmodel->getYearwisepds();
		
		$data['heading'] = 'Public distribution System';
		
		
		$data['data']=[];
		foreach ($pdses as $pds) {
			$data['data'][]=[
				'year_id'=>(int)$pds->distributed_year_id,
				'year'=>$pds->year,
				'districts'=>(int)$pds->total_district,
				'ration_card_holders'=>(int)$pds->total_chb,
				'qty_supply_pds'=>(float)$pds->total_quantity

			];
		}


		return $this->respond($data);
	}

	public function procurement2(){
		
		$districtmodel = new DistrictModel();

		$procuremodel = new ProcurementChartModel();
		$filter = [];
		$district_id=0;
		if ($this->request->getGet('district_id') && $this->request->getGet('district_id') > 0) {
			$filter['district_id'] = $district_id=$this->request->getGet('district_id');
		}
		$data['data']=[];
		$procure= $procuremodel->getYearwisedata($filter);
		foreach($procure as $proc){
			$data['data'][]=[
				'districts'=>(int)$proc->total_districts,
				'qty_proc'=>(int)$proc->total_quantity,
				'total_amt'=>(float)$proc->total_amount,
				'total_farmers'=>(int)$proc->total_farmers,
				'year'=>$proc->year,
				'year_id'=>(int)$proc->year_id,
			];
		}
		$data['heading'] = 'Procurement Details';
		
		if ($district_id) {
			$data['heading'] .= ' for:-' . $districtmodel->find($district_id)->name;
		}

		return $this->respond($data);
	}

	public function farmerareayear(){ 
		
		$areamodel = new AreaChartModel();
		$filter = [];
		
		$achievements = $areamodel->getYearwiseFarmerAchievement($filter);
		//dd($achievements);

		$data['heading'] = 'Trends of Total Farmers and Total Area Coverage';
		
		$data['data'] =[];
		foreach ($achievements as $achievement) {
			$data['data'][] =[
				'year_id'=>(int)$achievement->year_id,
				'year'=>$achievement->year_id,
				'total_area_coverage'=>(float)$achievement->total_ach,
				'total_farmers_coverage'=>(int)$achievement->total_farmers,
				'total_nursery_beds'=>(float)$achievement->total_nursery,
				'total_intercropping'=>(float)$achievement->total_intercropping

			];
		}
		
		return $this->respond($data);
	}

	public function mapdata(){
		
		$distmapmodel = new DistrictMapModel();
		$mapdatas = $distmapmodel->getMapData();
		
		$data['heading'] = 'Map Data';
		
		$data['data'] =[];
		foreach ($mapdatas as $mapdata) {
			$data['data'][] =[
				'district_id'=>(int)$mapdata->district_id,
				'district'=>$mapdata->district,
				'total_blocks'=>(int)$mapdata->blocks,
				'total_gps'=>(int)$mapdata->gps,
				'total_villages'=>(int)$mapdata->villages,
				'total_farmers'=>(int)$mapdata->farmers,
				'chc'=>(int)$mapdata->chcs,
				'cmsc'=>(int)$mapdata->cmscs,

			];
		}
		// dd($data);
		return $this->respond($data);
	}

	public function enterprises(){
		$enterprisemodel = new EnterpriseChartModel();;
		$filter = [];
		

		$enterprises = $enterprisemodel->getYearwisedata($filter);
		$data['data']=[];
		foreach ($enterprises as $enterprise) {
			$data['data'][]=[
				'year_id'=>$enterprise->year_id,
				'year'=>$enterprise->year,
				'unit_id'=>$enterprise->unit_id,
				'unit_name'=>$enterprise->unit_name,
				'total_fpounit'=>$enterprise->total_fpos,
				'total_shgunit'=>$enterprise->total_wshg
			];
		}
		//heading
		$data['heading'] = 'Crop Enterprise';
		

		return $this->respond($data);
	}

	public function summary(){
		$distmapmodel = new DistrictMapModel();
		$yearModel = new YearModel();
        $summery = $distmapmodel->getSummary();

        $data['data']=[
            'total_districts'=>(int)$summery->total_districts,
            'total_blocks'=>(int)$summery->total_blocks,
            'total_gps'=>(int)$summery->total_gps,
            'total_villages'=>(int)$summery->total_villages,
            'total_farmers'=>(int)$summery->total_farmers,
            'total_chc'=>(int)$summery->total_chc,
            'total_cmsc'=>(int)$summery->total_cmsc,
            'demo_area'=>(int)$summery->demo_area
        ];
		//heading
		$data['heading'] = 'Summary Data';
		

		return $this->respond($data);
	}

}
