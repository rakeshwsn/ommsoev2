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

		$this->template->set_meta_title(lang('Grampanchayat.heading_title'));
		return $this->getList();
	}
	private function _allblocks($blocks, &$data)
	{

		$total_farmers_covered = $total_nursery_raised = $total_balance_smi =
			$total_balance_lt = $total_ragi_smi = $total_ragi_lt = $total_ragi_ls =
			$total_little_millet_lt = $total_little_millet_ls = $total_foxtail_ls =
			$total_sorghum_ls = $total_kodo_ls = $total_barnyard_ls = $total_pearl_ls =
			$total_total_ragi = $total_total_non_ragi = $total_fc_area = $total_total_area = 0;

		$data['rows'] = [];
		$gps = 0;
		foreach ($blocks as $block) {
			$total_area = $block->fc_area +
				$block->ragi_smi +
				$block->ragi_lt +
				$block->ragi_ls +
				$block->little_millet_lt +
				$block->little_millet_ls +
				$block->foxtail_ls +
				$block->sorghum_ls +
				$block->kodo_ls +
				$block->barnyard_ls +
				$block->pearl_ls;
			$total_ragi = $block->ragi_smi +
				$block->ragi_lt +
				$block->ragi_ls;
			$total_non_ragi = $total_area - $total_ragi - $block->fc_area;

			$data['rows'][] = [
				'district' => $block->district,
				'block' => $block->block,
				'gps' => $block->total_gps,
				'farmers_covered' => $block->farmers_covered,
				'nursery_raised' => $block->nursery_raised,
				'balance_smi' => $block->balance_smi,
				'balance_lt' => $block->balance_lt,
				'ragi_smi' => $block->ragi_smi,
				'ragi_lt' => $block->ragi_lt,
				'ragi_ls' => $block->ragi_ls,
				'little_millet_lt' => $block->little_millet_lt,
				'little_millet_ls' => $block->little_millet_ls,
				'foxtail_ls' => $block->foxtail_ls,
				'sorghum_ls' => $block->sorghum_ls,
				'kodo_ls' => $block->kodo_ls,
				'barnyard_ls' => $block->barnyard_ls,
				'pearl_ls' => $block->pearl_ls,
				'total_ragi' => $total_ragi,
				'total_non_ragi' => $total_non_ragi,
				'total_fc' => $block->fc_area,
				'total_area' => $total_area
			];

			//calc total
			$total_farmers_covered += $block->farmers_covered;
			$total_nursery_raised += $block->nursery_raised;
			$total_balance_smi += $block->balance_smi;
			$total_balance_lt += $block->balance_lt;
			$total_ragi_smi += $block->ragi_smi;
			$total_ragi_lt += $block->ragi_lt;
			$total_ragi_ls += $block->ragi_ls;
			$total_little_millet_lt += $block->little_millet_lt;
			$total_little_millet_ls += $block->little_millet_ls;
			$total_foxtail_ls += $block->foxtail_ls;
			$total_sorghum_ls += $block->sorghum_ls;
			$total_kodo_ls += $block->kodo_ls;
			$total_barnyard_ls += $block->barnyard_ls;
			$total_pearl_ls += $block->pearl_ls;
			$total_total_ragi += $total_ragi;
			$total_total_non_ragi += $total_non_ragi;
			$total_fc_area += $block->fc_area;
			$total_total_area += $total_area;

			$gps += $block->total_gps;

		}

		$data['rows'][] = [
			'district' => '<strong>Total</strong>',
			'block' => '',
			'gps' => $gps,
			'farmers_covered' => $total_farmers_covered,
			'nursery_raised' => $total_nursery_raised,
			'balance_smi' => $total_balance_smi,
			'balance_lt' => $total_balance_lt,
			'ragi_smi' => $total_ragi_smi,
			'ragi_lt' => $total_ragi_lt,
			'ragi_ls' => $total_ragi_ls,
			'little_millet_lt' => $total_little_millet_lt,
			'little_millet_ls' => $total_little_millet_ls,
			'foxtail_ls' => $total_foxtail_ls,
			'sorghum_ls' => $total_sorghum_ls,
			'kodo_ls' => $total_kodo_ls,
			'barnyard_ls' => $total_barnyard_ls,
			'pearl_ls' => $total_pearl_ls,
			'total_ragi' => $total_total_ragi,
			'total_non_ragi' => $total_total_non_ragi,
			'total_fc' => $total_fc_area,
			'total_area' => $total_total_area
		];
	}
	protected function getList()
	{

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Grampanchayat.heading_title'),
			'href' => admin_url('grampanchayat')
		);

		$this->template->add_package(array('datatable', 'select2'), true);

		$data['edit'] = admin_url('areacoverage/target/edit');
		$data['add'] = admin_url('areacoverage/target/add');
		$data['heading_title'] = lang('Area Coverage Target');
		$data['button_add'] = lang('Add Target');
		$data['button_edit'] = lang('Edit Target');
		$data['years'] = getAllYears();
		$data['seasons'] = $this->acModel->getSeasons();

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
		if ($this->user->block_id) {
			$data['block_id'] = $filter['block_id'] = $this->user->block_id;
			$data['districts'] = $this->districtModel->where('id', $this->user->district_id)->asArray()->find();
		} else if ($this->user->district_id) {
			$data['district_id'] = $filter['district_id'] = $this->user->district_id;
			$data['districts'] = $this->districtModel->where('id', $this->user->district_id)->asArray()->find();
		} else {
			$data['districts'] = $this->districtModel->asArray()->find();
		}
		$data['current_season'] = strtolower(getCurrentSeason());
		$data['year_id'] = getCurrentYearId();
		$filter = [
			'year_id' => $data['year_id'],
			'season' => $data['current_season']
		];
		$blocks = $this->acModel->getByDistrict($filter);
		$this->_allblocks($blocks, $data);
		$data['blocks'] = [];
		if ($data['district_id']) {
			$data['blocks'] = $this->blockModel->where('district_id', $data['district_id'])
				->asArray()->findAll();
		}



		$data['year_id'] = date('Y');

		// $currentMonth = date('n');
		// if ($currentMonth >= 6 && $currentMonth <= 10) {
		// 	$season = 'Kharif';
		// } elseif ($currentMonth >= 11 && $currentMonth <= 4) {
		// 	$season = 'Rabi';
		// }

		$data['current_season'] = strtolower(getCurrentSeason());


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

		$data['filter_panel'] = view('Admin\Reports\Views\areacoverage_filter', $data);

		return $this->template->view('Admin\CropCoverage\Views\areacoverage_target', $data);
	}

	public function edit()
	{
		if ($this->request->getMethod(1) === 'POST') {


			$block_id = $this->request->getGet('block_id');
			$data['block_id'] = $block_id;

			$masterdata = array(
				"district_id" => $this->user->district_id,
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


			$data['crop_data'] = $this->request->getPost('crop');


			$this->targetModel->addTargets($data, $target_id);

			$data['followup_data'] = $this->request->getPost('followup');


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



		$output = array();
		foreach ($data['practicedata'] as $practice) {
			$crop_id = $practice['id'];

			// Check if the crop_id exists in $crop array
			if (array_key_exists($crop_id, $data['croppractices'])) {
				// If the crop_id exists, get the crop values from $crop array
				$crop_values = $data['croppractices'][$crop_id];

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