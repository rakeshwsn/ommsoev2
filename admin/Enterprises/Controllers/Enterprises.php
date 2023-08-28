<?php

namespace Admin\Enterprises\Controllers;
use Admin\Enterprises\Models\BlockModel;
use Admin\Enterprises\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesBudgetModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\GpModel;
use Admin\Enterprises\Models\VillagesModel;
use Admin\Enterprises\Models\YearModel;
use App\Controllers\AdminController;

class Enterprises extends AdminController
{
	public function index()
	{
		helper('form');
		$enterprisesmodel = new EnterprisesModel();
		$distModel = new DistrictModel();
		$data['districts'][0] = 'Select District';

		$districts = $distModel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}

		$blockmodel = new BlockModel();
		$data['blocks'][0] = 'Select Block';

		$data['district_id'] = 0;
		$data['block_id'] = 0;

		if ($this->request->getGet('district_id')) {
			$data['district_id'] = $this->request->getGet('district_id');
			$blocks = $blockmodel->where('district_id', $data['district_id'])->findAll();

			$data['blocks'][0] = 'Select Block';
			foreach ($blocks as $block) {
				$data['blocks'][$block->id] = $block->name;
			}
		}
		if ($this->request->getGet('block_id')) {
			$data['block_id'] = $this->request->getGet('block_id');
		}

		$filter = [];
		if ($data['district_id'] > 0) {
			$filter['district_id'] = $data['district_id'];
		}

		if ($data['block_id'] > 0) {
			$filter['block_id'] = $data['block_id'];
		}
		$enterpriseslist = $enterprisesmodel->getAll($filter);
		// dd($enterpriseslist);
		$data['enterprises'] = [];

		foreach ($enterpriseslist as $row) {
			$data['enterprises'][] = [
				'districts' => $row->districts,
				'blocks' => $row->blocks,
				'gp' => $row->gp,
				'villages' => $row->villages,
				'management_unit_type' => $row->management_unit_type,
				'managing_unit_name' => $row->managing_unit_name,
				'date_estd' => $row->date_estd,
				'mou_date' => $row->mou_date,
				'edit_url' => admin_url('enterprises/edit?id=' . $row->id),
				
			];
		}



		return $this->template->view('Admin\Enterprises\Views\establishment', $data);
	}
	public function add()
	{


		if ($this->request->getMethod(1) == 'POST'  && $this->validateForm()) {
			// dd($this->request->getPost());
			$enterprisesmodel = new EnterprisesModel();
			$enterprisesmodel->where('id', $this->request->getGet('id'))->delete();

			$enterprisesdata[] = [
				'unit_id' => $this->request->getPost('unit_id'),
				'district_id' => $this->request->getPost('district_id'),
				'block_id' => $this->request->getPost('block_id'),
				'gp_id' => $this->request->getPost('gp_id'),
				'village_id' => $this->request->getPost('village_id'),
				'budget_fin_yr_id' => $this->request->getPost('budget_fin_yr_id'),
				'management_unit_type' => $this->request->getPost('management_unit_type'),
				'managing_unit_name' => $this->request->getPost('managing_unit_name'),
				'contact_person' => $this->request->getPost('contact_person'),
				'contact_mobile' => $this->request->getPost('contact_mobile'),
				'date_estd' => $this->request->getPost('date_estd'),
				'mou_date' => $this->request->getPost('mou_date'),
				'unit_budget' => $this->request->getPost('unit_budget'),
				'addl_budget' => $this->request->getPost('addl_budget'),
				'unit_budget_amount' => $this->request->getPost('unit_budget_amount'),
				'is_support_basis_infr' => $this->request->getPost('is_support_basis_infr'),
				'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
				'support_infr_amount' => $this->request->getPost('support_infr_amount')
			];
			// dd($enterprisesdata);

			$enterprisesmodel->insertBatch($enterprisesdata);

			return redirect()->to(admin_url('enterprises'))->with('message', 'successful');
		}

		return $this->getForm();
	}
	public function edit()
	{

		$enterprisesmodel = new EnterprisesModel();
		if ($this->request->getMethod(1) == 'POST'  && $this->validateForm()) {
			$id = $this->request->getGet('id');
			$district_id = $this->request->getPost('district_id');
			$block_id = $this->request->getPost('block_id');
			$enterprisesmodel->where('id', $id)->delete();
			$enterprisesdata[] = [
				'unit_id' => $this->request->getPost('unit_id'),
				'district_id' => $district_id,
				'block_id' => $block_id,
				'gp_id' => $this->request->getPost('gp_id'),
				'village_id' => $this->request->getPost('village_id'),
				'budget_fin_yr_id' => $this->request->getPost('budget_fin_yr_id'),
				'management_unit_type' => $this->request->getPost('management_unit_type'),
				'managing_unit_name' => $this->request->getPost('managing_unit_name'),
				'contact_person' => $this->request->getPost('contact_person'),
				'contact_mobile' => $this->request->getPost('contact_mobile'),
				'date_estd' => $this->request->getPost('date_estd'),
				'mou_date' => $this->request->getPost('mou_date'),
				'unit_budget' => $this->request->getPost('unit_budget'),
				'addl_budget' => $this->request->getPost('addl_budget'),
				'unit_budget_amount' => $this->request->getPost('unit_budget_amount'),
				'is_support_basis_infr' => $this->request->getPost('is_support_basis_infr'),
				'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
				'support_infr_amount' => $this->request->getPost('support_infr_amount')
			];

			$data['enterprises'] = $enterprisesmodel->insertBatch($enterprisesdata);

			return redirect()->to(admin_url('enterprises'))->with('message', 'successful');
		}


		return $this->getForm();
	}

	public function ajaxBlocks()
	{

		$data['blocks'] = [];
		$BlocksModel = new BlockModel();

		$district_id = $this->request->getGet('district_id');

		$data['blocks'] = $BlocksModel->where('district_id', $district_id)->findAll();

		return $this->response->setJSON($data);
	}
	public function ajaxgps()
	{

		$data['gps'] = [];
		$gpmodel = new GpModel();

		$block_id = $this->request->getGet('block_id');

		$data['gps'] = $gpmodel->where('block_id', $block_id)->findAll();

		return $this->response->setJSON($data);
	}
	public function ajaxvillages()
	{

		$data['villages'] = [];
		$villagemodel = new VillagesModel();

		$gp_id = $this->request->getGet('gp_id');

		$data['villages'] = $villagemodel->where('gp_id', $gp_id)->findAll();

		return $this->response->setJSON($data);
	}

	private function getForm()
	{
		$enterprisesmodel = new EnterprisesModel();
		// district start
		$districtmodel = new DistrictModel();
		$data['districts'][0] = 'Select districts';

		$districts = $districtmodel->findAll();

		foreach ($districts as $district) {
			$data['districts'][$district->id] = $district->name;
		}
		$yearmodel = new YearModel();
		$data['budget_fin_yrs'][0] = 'Select Budget Year';

		$budget_fin_yrs = $yearmodel->findAll();

		foreach ($budget_fin_yrs as $budget_fin_yr) {
			$data['budget_fin_yrs'][$budget_fin_yr->id] = $budget_fin_yr->name;
		}
		//district end
		//block start
		$blockmodel = new BlockModel();
		$data['blocks'][0] = 'Select blocks';

		if ($this->request->getPost('district_id')) {
			$blockModel = new BlockModel();

			$blocks = $blockModel->where('district_id', $this->request->getPost('district_id'))->findAll();

			foreach ($blocks as $block) {

				$data['blocks'][$block->id] = $block->name;
			}
		}
		$data['block_id'] = 0;
		if ($this->request->getPost('block_id')) {
			$data['block_id'] = $this->request->getPost('block_id');
		}
		//block end
		//gp start
		$data['gps'][0] = 'Select Gp';

		if ($this->request->getPost('gp_id')) {
			$gpmodel = new GpModel();

			$gps = $gpmodel->where('block_id', $this->request->getPost('block_id'))->findAll();

			foreach ($gps as $gp) {

				$data['gps'][$gp->id] = $gp->name;
			}
		}
		$data['gp_id'] = 0;
		if ($this->request->getPost('gp_id')) {
			$data['gp_id'] = $this->request->getPost('gp_id');
		}
		//gp end
		//villages start
		$data['villages'][0] = 'Select villages';

		if ($this->request->getPost('gp_id')) {
			$villagemodel = new VillagesModel();

			$villages = $villagemodel->where('gp_id', $this->request->getPost('gp_id'))->findAll();

			foreach ($villages as $village) {

				$data['villages'][$village->id] = $village->name;
			}
		}
		$data['village_id'] = 0;
		if ($this->request->getPost('village_id')) {
			$data['village_id'] = $this->request->getPost('village_id');
		}
		//village end
		$enterprisesbudgetmodel = new EnterprisesBudgetModel();
		$data['unit_budgets'][0] = 'Select budgets';
		// $budget_id = [];
		$unit_budgets = $enterprisesbudgetmodel->findAll();

		foreach ($unit_budgets as $unit_budget) {
			$data['unit_budgets'][$unit_budget->id] = $unit_budget->budget_code;
		}
		$enterprisesbudgetmodel = new EnterprisesBudgetModel();
		$data['addl_budgets'][0] = 'Select budgets';
		// $budget_id = [];
		$addl_budgets = $enterprisesbudgetmodel->findAll();

		foreach ($addl_budgets as $addl_budget) {
			$data['addl_budgets'][$addl_budget->id] = $addl_budget->budget_code;
		}

		$enterprisesunitmodel = new EnterprisesUnitModel();
		$data['units'][0] = 'Select Units';

		$units = $enterprisesunitmodel->findAll();

		foreach ($units as $unit) {
			$data['units'][$unit->id] = $unit->name;
		}
		$data['management_unit_types'] = [
			'SHG' => 'SHG',
			'FPO' => 'FPO',
		];
		$data['is_support'] = [
			'no' => 'No',
			'yes' => 'Yes',
		];
		// dd($data);
		$id = [];
		if ($this->request->getGet('id')) {
			$enterprise =  $enterprisesmodel->where('id', $this->request->getGet('id'))->first();
			// printr($data); exit;
			$data['enterprise_text'] = "Edit Enterprise Data";

			foreach ($enterprise as $col => $value) {
				$data[$col] = $value;
			}
		} else {
			$enterprise = $enterprisesmodel->db->getFieldData('enterprises');
			// dd($enterprise);
			foreach ($enterprise as $value) {
				$data['enterprise_text'] = "Add Enterprise Data";
				$data[$value->name] = '';
			}
		}
		//  dd($data);
		return $this->template->view('Admin\Enterprises\Views\addEstablishment', $data);
	}


	protected function validateForm()
	{
		$enterprisesmodel = new EnterprisesModel();
		$validation =  \Config\Services::validation();
		$id = $this->uri->getSegment(4);

		$rules = $enterprisesmodel->validationRules;
		// dd($this->request->getPost());
		if ($this->validate($rules)) {
			return true;
		} else {
			// dd($validation->getErrors());
			$this->error['warning'] = "Warning: Please check the form carefully for errors!";
			return false;
		}
		return !$this->error;
	}
}
