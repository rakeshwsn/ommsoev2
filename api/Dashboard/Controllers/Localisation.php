<?php

namespace Api\Dashboard\Controllers;

use Api\Dashboard\Models\BlockModel;
use Api\Dashboard\Models\AreaChartModel;
use Api\Dashboard\Models\CurrentYearChartModel;
use Api\Dashboard\Models\DashboardChartModel;
use Api\Dashboard\Models\DistrictMapModel;
use Api\Dashboard\Models\EstablishmentChartModel;
use Api\Dashboard\Models\PdsChartModel;
use Api\Dashboard\Models\ProcurementChartModel;
use Api\Dashboard\Models\EnterpriseChartModel;
use Api\Dashboard\Models\DistrictModel;
use Api\Dashboard\Models\OdMapModel;
use Api\Dashboard\Models\YearModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Localisation extends ResourceController
{
	use ResponseTrait;
	private $apiModel;
	private $user;

	public function __construct()
	{
		helper("aio");
		$this->user = service('user');
		Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
		Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
		Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
	}

	/**
	 * This function is responsible for retrieving all districts and blocks for the API.
	 * Implemented by Rakesh Nayak.
	 */
	public function localisation()
	{
		// Create new instances of DistrictModel and BlockModel
		$districtmodel = new DistrictModel();
		$blockmodel = new BlockModel();

		// Get all districts and blocks
		$data['allDistricts'] =  $districtmodel->getAllDistrict();
		$data['allBlocks'] =  $blockmodel->getAllBlock();

		// Return the data
		return $this->respond($data);
	}
}
