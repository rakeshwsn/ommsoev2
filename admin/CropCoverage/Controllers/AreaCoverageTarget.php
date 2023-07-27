<?php
namespace Admin\CropCoverage\Controllers;

use Admin\CropCoverage\Models\AreaCoverageModel;
use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;
use Admin\CropCoverage\Models\CropsModel;
use Admin\CropCoverage\Models\PracticesModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;

class AreaCoverageTarget extends AdminController
{
	private $data;
	private $error = array();
	private $targetModel;
	private $blockModel;
	private $districtModel;
	private $cropsModel;
	private $practicesModel;

	function __construct()
	{
		$this->targetModel = new TargetModel();
		$this->blockModel = new BlockModel();
		$this->districtModel = new DistrictModel();
		$this->cropsModel = new CropsModel;
		$this->practicesModel = new PracticesModel;
	}
	public function Index()
	{

		$this->template->set_meta_title(lang('Grampanchayat.heading_title'));
		return $this->getList();
	}
	protected function getList()
	{

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

		// $data['add'] = admin_url('areacoverage/target/add');
		$data['edit'] = admin_url('areacoverage/target/edit');
		$data['add'] = admin_url('areacoverage/target/add');
		$data['heading_title'] = lang('Area Coverage Target');
		$data['button_add'] = lang('Add Target');
		$data['button_edit'] = lang('Edit Target');


		if (isset($this->error['warning'])) {
			$data['error'] = $this->error['warning'];
		}

		if ($this->request->getGet('district_id')) {
			$data['district_id'] = (array) $this->request->getGet('district_id');
		} else {
			$data['district_id'] = $this->user->district_id;
		}

		$croppractices = $this->targetModel->getPractices();

		$practicedata = $this->targetModel->getAll([
			'district_id' => $data['district_id']
		]);

		$data['practicedata'] = $practicedata;
		// printr($practicedata);
		// exit;

		$data['year_id'] = date('Y');

		$currentMonth = date('n');
		if ($currentMonth >= 6 && $currentMonth <= 10) {
			$season = 'Kharif';
		} elseif ($currentMonth >= 11 && $currentMonth <= 4) {
			$season = 'Rabi';
		}
		$data['season'] = $season;

		//for heading
		$crops = [];
		foreach ($croppractices as $cp) {
			$_crops = $cp['crops'];

			if (!isset($crops[$_crops])) {
				$crops[$_crops] = array();
			}

			$crops[$_crops][] = $cp['practice'];
		}

		$data['heading'] = $crops;

		// printr($crops);
// 		exit;
		return $this->template->view('Admin\CropCoverage\Views\areacoverage_target', $data);
	}
	public function add()
	{
		if ($this->request->getMethod(1) === 'POST') {
			printr($_POST);
			exit;

			$data['block_id'] = $this->request->getGet('block_id');
			$data['crop_data'] = $this->request->getPost('crop');

			$this->targetModel->addTargets($data);

			$this->session->setFlashdata('message', 'Target Saved Successfully.');

			return redirect()->to(base_url('admin/areacoverage/target'));
		}
		$this->getForm();
	}
	public function edit()
	{
		if ($this->request->getMethod(1) === 'POST') {

			// printr($_POST);
			// exit;
			//delete existing
			$block_id = $this->request->getGet('block_id');
			$data['block_id'] = $block_id;
			$masterdata = array(
				"block_id" => $data['block_id'],
				"year_id" => getCurrentYearId(),
				"season" => getCurrentSeason(),
			);
			$master = $this->targetModel->where($masterdata)->first();
			if ($master) {
				$target_id = $master->id;
			} else {
				$target_id = $this->targetModel->insert($masterdata);
			}

			//insert new
			// printr($this->request->getPost());
			// exit;
			$data['crop_data'] = $this->request->getPost('crop');

			$this->targetModel->addTargets($data, $target_id);

			$data['followup_data'] = $this->request->getPost('followup');
			// printr($data['followup_data']);
			// exit;

			$this->targetModel->addFollowUpCrops($data, $target_id);


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

		$data['district_id'] = $this->user->district_id;
		$data['block_id'] = $this->request->getGet('block_id');
		$data['year_id'] = date('Y');

		$currentMonth = date('n');
		if ($currentMonth >= 6 && $currentMonth <= 10) {
			$season = 'Kharif';
		} elseif ($currentMonth >= 11 && $currentMonth <= 4) {
			$season = 'Rabi';
		}
		$data['season'] = $season;

		$data['croppractices'] = (new AreaCoverageModel)->getCropPractices();

		// Pass the practice data to the view
		$data['practicedata'] = $this->targetModel->getBlockTargets([
			'block_id' => $data['block_id'],
			'season' => getCurrentSeason(),
			'year_id' => getCurrentYearId()
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
				// printr($crop_values);
				// exit;
				// Define the fields to check
				$fields = ['smi', 'lt', 'ls', 'followup'];

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

		$data['blocks'] = (new BlockModel())->getBlocksByDistrict($data['district_id']);
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