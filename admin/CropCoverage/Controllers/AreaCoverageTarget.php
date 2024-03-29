<?php

namespace Admin\CropCoverage\Controllers;

use Admin\Common\Models\YearModel;
use Admin\CropCoverage\Models\AreaCoverageModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use MicrosoftAzure\Storage\Blob\Models\Block;

class AreaCoverageTarget extends AdminController
{
	private $data;
	private $error = array();
	private $targetModel;
	private $blockModel;
	private $districtModel;
	private $cropsModel;
	private $practicesModel;
	private $acModel;


	function __construct()
	{
		$this->acModel = new AreaCoverageModel();
		$this->targetModel = new TargetModel();
		$this->blockModel = new BlockModel();
		$this->districtModel = new DistrictModel();
		$this->cropsModel = new CropsModel;
		$this->practicesModel = new PracticesModel;
	}
	public function Index()
	{
		$this->template->set_meta_title(lang('Crop Coverage|Target'));

		return $this->getList();

	}
	protected function getList()
	{
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			//'text' => lang('Grampanchayat.heading_title'),
			// 'href' => admin_url('grampanchayat')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

		$data['edit'] = admin_url('areacoverage/target/edit');
		$data['add'] = admin_url('areacoverage/target/add');
		$data['heading_title'] = lang('Area Coverage Target');
		$data['button_add'] = lang('Add Target');
		$data['button_edit'] = lang('Edit Target');
		$data['years'] = getAllYears();
		$data['seasons'] = $this->acModel->getSeasons();
		if ($this->request->getGet('season')) {
			$data['current_season'] = $season = $this->request->getGet('season');
		} else {
			$data['current_season'] = strtolower(getCurrentSeason());
		}

		//$data['get_season'] = $this->request->getGet('season') ?? $data['current_season'];
		// printr($data['get_season']);
		// exit;
		$croppractices = $this->targetModel->getPractices();

		// echo $data['season'];
		// exit;
		$data['target_url'] = admin_url('areacoverage/target/filter');
		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}
		if ($this->request->getGet('district_id')) {
			$data['district_id'] = (array) $this->request->getGet('district_id');
		} elseif ($this->user->district_id) {
			$data['district_id'] = $this->user->district_id;
		} else {
			$data['district_id'] = 0;
		}
		$filter = [
			'season' => $this->request->getGet('season') ?? $data['current_season'],
			'year_id' => $this->request->getGet('year_id') ?? getCurrentYearId(),
			'district_id' => $data['district_id']

		];
		// printr($filter);
		// exit;
		if ($data['district_id'] === 0) {
			$distwisetarget = $this->targetModel->getDistrictWiseData($filter);
			// dd($distwisetarget);
			// exit;
		} else {
			$blockstarget = $this->targetModel->viewBlockTarget($filter);

		}
		// printr($blockstarget);
		// exit;
		if ($data['district_id'] === 0) {
			$data['distwisetarget'] = $distwisetarget;
		} else {
			$data['blockstarget'] = $blockstarget;
		}

		$crops = [];
		foreach ($croppractices as $cp) {
			$_crops = $cp['crops'];

			if (!isset($crops[$_crops])) {
				$crops[$_crops] = array();
			}

			$crops[$_crops][] = $cp['practice'];
		}
		$data['heading'] = $crops;
		return $this->template->view('Admin\CropCoverage\Views\areacoverage_target', $data);
	}


	public function edit()
	{

		if ($this->request->getMethod(1) === 'POST') {

			// printr($_POST);
			// exit;
			$block_id = $this->request->getGet('block_id');
			$data['block_id'] = $block_id;

			$masterdata = array(
				"district_id" => $this->user->district_id,
				"block_id" => $data['block_id'],
				"year_id" => getCurrentYearId(),
				"season" => $this->request->getGet('season') ?? getCurrentSeason(),
			);
			// printr($masterdata);
			// exit;

			$master = $this->targetModel->where($masterdata)->first();
			if ($master) {
				$target_id = $master->id;
			} else {
				$target_id = $this->targetModel->insert($masterdata);
			}


			$data['crop_data'] = $this->request->getPost('crop');
			// printr($data['crop_data']);
			// exit;

			$this->targetModel->addTargets($data, $target_id);

			$data['followup_data'] = $this->request->getPost('followup');


			$this->targetModel->addFollowUpCrops($data, $target_id);
			$data['rice_fallow_data'] = $this->request->getPost('rice_fallow');

			// printr($data['rice_fallow_data']);
			// exit;
			$this->targetModel->addRiceFallowCrops($data, $target_id);


			$this->session->setFlashdata('message', 'Target Updated Successfully.');

			return redirect()->to(base_url('admin/areacoverage/target'));
		}
		$this->getForm();
	}
	protected function getForm()
	{
		$_SESSION['isLoggedIn'] = true;

		$data['cancel'] = admin_url('areacoverage/target');

		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}
		$data['crops'] = $this->cropsModel->GetCrops();
		$data['practices'] = $this->practicesModel->GetPractices();
		if ($this->request->getGet('season')) {
			$data['season'] = $this->request->getGet('season');
		} else {
			$data['season'] = strtolower(getCurrentSeason());
		}
		// print_r($data['season']);
		// exit;
		$data['district_id'] = $this->user->district_id;
		$data['block_id'] = $this->request->getGet('block_id');
		// print_r($data['block_id']);
		// exit;
		// $data['season'] = $this->request->getGet('season');
		// print_r($data['season']);
		// exit;
		$data['year_id'] = date('Y');
		// $data['seasons'] = $this->acModel->getSeasons();
		// $data['current_season'] = strtolower(getCurrentSeason());


		$data['croppractices'] = $this->acModel->getCropPractices();
		// print_r($data['croppractices']);
		// exit;
		// Pass the practice data to the view
		// echo getCurrentSeason();
		// exit;
		$data['practicedata'] = $this->targetModel->getBlockTargets([

			'block_id' => $data['block_id'],
			'season' => $data['season'],
			'year_id' => $this->request->getGet('year_id') ?? getCurrentYearId(),
		]);
		// printr($data['practicedata']);
		// exit;



		$output = array();
		foreach ($data['practicedata'] as $practice) {
			$crop_id = $practice['id'];

			// Check if the crop_id exists in $crop array
			if (array_key_exists($crop_id, $data['croppractices'])) {
				// If the crop_id exists, get the crop values from $crop array
				$crop_values = $data['croppractices'][$crop_id];

				$fields = ['smi', 'lt', 'ls', 'followup', 'rice_fallow'];

				// Initialize an empty array to store the values for 'smi', 'lt', and 'ls'
				$values = array();

				// Loop through the fields and set the values and statuses
				foreach ($fields as $field) {
					$status = in_array($field, $crop_values) ? 1 : 0;
					$values[$field] = ['value' => $practice[$field], 'status' => $status];
				}

				// Assign the values to the corresponding keys in $practice array
				$practice['smi'] = $values['smi'];
				$practice['lt'] = $values['lt'];
				$practice['ls'] = $values['ls'];
				$practice['followup'] = $values['followup'];
				$practice['rice_fallow'] = $values['rice_fallow'];

				// Add the updated $practice array to the $output array
				$output[] = $practice;
			}
		}

		$data['practicedata'] = $output;
		// printr($data['practicedata']);
		// exit;





		if (empty($this->user->district_id)) {
			$data['missingDistrictId'] = true;
		} else {
			$data['missingDistrictId'] = false;
			$data['district_id'] = $this->user->district_id;
		}
		$data['districts'] = $this->districtModel->getAll();

		$data['blocks'] = $this->blockModel->getBlocksByDistrict($data['district_id']);
		$data['block_id'] = $this->user->block_id;


		echo $this->template->view('Admin\CropCoverage\Views\targetform', $data);
	}

	public function fetchBlocks()
	{
		if ($this->request->isAJAX()) {
			$districtId = $this->request->getPost('districtId');

			// Fetch blocks from the BlockModel based on district ID

			$blocks = $blockModel->where('district_id', $districtId)->findAll();

			// Prepare options array for the block dropdown
			$blockOptions = [];
			foreach ($blocks as $block) {
				$blockOptions[$block->id] = $block->name;
			}

			// Return blocks as JSON response
			return $this->response->setJSON(['success' => true, 'blocks' => $blockOptions]);
		} else {
			// Invalid request
			return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
		}
	}


}
?>