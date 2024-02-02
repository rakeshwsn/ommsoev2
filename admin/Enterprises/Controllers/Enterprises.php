<?php

namespace Admin\Enterprises\Controllers;

use Admin\Dashboard\Models\BlockModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesBudgetModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\EnterpriseGpModel;
use Admin\Enterprises\Models\EnterpriseVillagesModel;
use Admin\Dashboard\Models\YearModel;
use Admin\Localisation\Controllers\LgdGps;
use Admin\Localisation\Models\GrampanchayatModel;
use Admin\Localisation\Models\LgdBlocksModel;
use Admin\Localisation\Models\LgdGpsModel;
use Admin\Localisation\Models\LgdVillagesModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Admin\Enterprises\Models\EnterprisesEquipmentModel;

use Admin\Equipment\Models\EquipmentModel;

class Enterprises extends AdminController
{
    public function index()
    {
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
        $this->template->set('header', true);
        helper('form');
        $enterprisesmodel = new EnterprisesModel();
        $distModel = new DistrictModel();
        $blockmodel = new BlockModel();
        $gpmodel = new LgdGpsModel();

        $enterprisesunitmodel = new EnterprisesUnitModel();
        //populate districts

        $districtmodel = new DistrictModel();
        $data['districts'][0] = 'Select Districts';

        if ($this->user->district_id) {
            $districts = $districtmodel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
        } else {
            $districts = $districtmodel->orderBy('name', 'asc')->findAll();
        }
      
        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $data['district_id'] = $this->user->district_id;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }
        
        $data['units'][0] = 'Select Unit Type';
        $units = $enterprisesunitmodel->orderBy('name', 'asc')->findAll();
       
        foreach ($units as $unit) {
            $data['units'][$unit->id] = $unit->name;
        }

        $data['unit_id'] = 0;


        //populate blocks of district selected
        $data['blocks'][0] = 'Select Block';
        if ($data['district_id']) {

            $blocks = $blockmodel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
            
            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        }


        //get years from district selected
        $data['years'][0] = 'Select DOE';
        if ($this->request->getGet('district_id')) {
            $district_id = $this->request->getGet('district_id');
            $yeardata = $enterprisesmodel->yearWise($district_id);

            foreach ($yeardata as $year) {
                $data['years'][] = $year->year;
            }
        }

        $data['block_id'] = $this->user->block_id;
        if ($this->request->getGet('block_id')) {
            $data['block_id'] = $this->request->getGet('block_id');
        }

        //for block login
        if($this->user->block_id){

        }

        $data['management_unit_type'] = '';
        if ($this->request->getGet('management_unit_type')) {
            $data['management_unit_type'] = $this->request->getGet('management_unit_type');
        }

        $data['doeyear'] = 0;
        if ($this->request->getGet('doeyear')) {
            $data['doeyear'] = $this->request->getGet('doeyear');
        }
        $data['unit_id'] = 0;
        if ($this->request->getGet('unit_id')) {
            $data['unit_id'] = $this->request->getGet('unit_id');
        }

        $filteredData = $this->filter();

        $data['enterprises'] = [];

        foreach ($filteredData as $row) {
            $data['enterprises'][] = [
                'districts' => $row->districts,
                'blocks' => $row->blocks,
                'gps' => $row->gps,
                'villages' => $row->villages,
                'unit_name' => $row->unit_name,
                'management_unit_type' => $row->management_unit_type,
                'managing_unit_name' => $row->managing_unit_name,
                'date_estd' => $row->date_estd,
                'mou_date' => $row->mou_date,
                'edit_url' => admin_url('enterprises/edit?id=' . $row->id),
            ];
        }

        $data['excel_link'] = admin_url('enterprises/exceldownld');
        $jsonData = json_encode($data);

        // dd($jsonData);
        return $this->template->view('Admin\Enterprises\Views\enterprise_index', $data);
    }

    public function search()
    {
        $this->enterprisesmodel = new EnterprisesModel();
        $requestData = $_REQUEST;
        $totalData = $this->enterprisesmodel->getTotals();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'district_id' => $requestData['district_id'],
            'block_id' => $requestData['block_id'],
            'unit_id' => $requestData['unit_id'],
            'management_unit_type' => $requestData['management_unit_type'],
            'doeyear' => $requestData['doeyear'],
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );
        $totalFiltered = $this->enterprisesmodel->getTotals($filter_data);

        $filteredData = $this->enterprisesmodel->getAll($filter_data);

        $datatable = array();
        foreach ($filteredData as $result) {

            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .= '<a class="btn btn-sm btn-primary" href="' . admin_url('block/edit/' . $result->id) . '"><i class="fa fa-pencil"></i></a>';
            $action .= '<a class="btn-sm btn btn-danger btn-remove" href="' . admin_url('block/delete/' . $result->id) . '" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[] = array(
                $result->districts,
                $result->blocks,
                $result->gps,
                $result->villages,
                $result->unit_name,
                $result->management_unit_type,
                $result->managing_unit_name,
                $result->date_estd,
                $result->mou_date,
                $action,
            );
        }
        //printr($datatable);
        $json_data = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        );

        return $this->response->setContentType('application/json')
            ->setJSON($json_data);
    }

    private function filter()
    {
        $enterprisesmodel = new EnterprisesModel();
        $filter = [];

        // if $this user has district_id, then filter by district_id
        if ($this->user->district_id) {
            $filter['district_id'] = $this->user->district_id;
        }

        if ($this->request->getGet('district_id') > 0) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        if ($this->request->getGet('unit_id') > 0) {
            $filter['unit_id'] = $this->request->getGet('unit_id');
        }

        if ($this->request->getGet('block_id') > 0) {
            $filter['block_id'] = $this->request->getGet('block_id');
        }



        if ($this->request->getGet('management_unit_type') != 'all') {
            $filter['management_unit_type'] = $this->request->getGet('management_unit_type');
        }
        if ($this->request->getGet('doeyear') > 0) {
            $filter['doeyear'] = $this->request->getGet('doeyear');
        }


        $filteredData = $enterprisesmodel->getAll($filter);
        // dd($filteredData);
        return $filteredData;
    }

    public function download()
    {
        $enterprisesmodel = new EnterprisesModel();

        $filteredData = $this->filter();

        $worksheet_unit = [];
        $data['entdatas'] = [];

        foreach ($filteredData as $row) {
            $data['entdatas'][] = [
                'unit_name' => $row->unit_name,
                'districts' => $row->districts,
                'blocks' => $row->blocks,
                'gps' => $row->gps,
                'villages' => $row->villages,
                'management_unit_type' => $row->management_unit_type,
                'managing_unit_name' => $row->managing_unit_name,
                'date_estd' => $row->date_estd,
                'mou_date' => $row->mou_date,
                'contact_person' => $row->contact_person,
                'unit_budget' => $row->unit_budget,
                'addl_budget' => $row->addl_budget,
                'purpose_infr_support' => $row->is_support_basis_infr,
                'support_infr_amount' => $row->is_support_basis_infr,
                'contact_mobile' => $row->contact_mobile,
            ];
        }

        $filename = 'Enterprise-Establishment' . '.xlsx';


        $sheetindex = 0;
        $reader = new Html();
        $doc = new \DOMDocument();
        $spreadsheet = new Spreadsheet();

        $htmltable = view('Admin\Enterprises\Views\excelFormEnt', $data);

        $htmltable = preg_replace("/&(?!\S+;)/", "&amp;", $htmltable);

        $worksheet = $spreadsheet->createSheet($sheetindex);

        $reader->setSheetIndex($sheetindex);
        $worksheet->setTitle("Enterprises");

        $spreadsheet = $reader->loadFromString($htmltable, $spreadsheet);

        $worksheet = $spreadsheet->getActiveSheet();

        // Load HTML content into a DOM object for formatting from class
        $doc->loadHTML($htmltable);

        $rows = $doc->getElementsByTagName('tr');

        //formatting and designing
        foreach ($worksheet->getRowIterator() as $row) {
            // Find the corresponding row element in the HTML table
            $rowIndex = $row->getRowIndex();

            $rowElement = $rows->item($rowIndex - 1); // -1 because row indices start at 1 in PhpSpreadsheet

            // Get the class name of the row element
            $className = $rowElement->getAttribute('class');
        }
        // Assuming $worksheet is your PhpSpreadsheet worksheet object

        // Get the highest column index
        $highestColumn = $worksheet->getHighestColumn();

        // Iterate through each column and set auto-size
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        //remove the default worksheet
        $spreadsheet->removeSheetByIndex(
            $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet')
            )
        );

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }


    public function add()
    {
        if ($this->request->getMethod(1) == 'POST') {

            $enterprisesmodel = new EnterprisesModel();
            $entequipmentmodel = new EnterprisesEquipmentModel();

            $enterprisesmodel->where('id', $this->request->getGet('id'))->delete();


            $enterprisesdata = [
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
                'is_support_basis_infr' => (int)$this->request->getPost('is_support_basis_infr'),
                'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
                'support_infr_amount' => $this->request->getPost('support_infr_amount'),
                'own_share' => $this->request->getPost('own_share'),
                'center_type' => $this->request->getPost('center_type'),
                'main_center_id' => $this->request->getPost('main_center_id'),


            ];

            $enterprisesmodel->insert($enterprisesdata);

            $unitId = $enterprisesmodel->insertID();

            $equipmentData = [];

            if ($unitId) {
                $equipments = $this->request->getPost('equipments');
                $quantities = $this->request->getPost('quantity');

                // Check if equipment details are available and they are arrays
                if ($equipments && $quantities && is_array($equipments) && is_array($quantities)) {
                    // Loop through equipment details and quantities
                    foreach ($equipments as $index => $equipment) {
                        // Use the same index for both equipments and quantities arrays
                        $quantity = $quantities[$index] ?? 0;

                        $equipmentData[] = [
                            'ent_id' => $unitId,
                            'equipment_id' => $equipment,
                            'quantity' => $quantity,
                        ];
                    }
                    $entequipmentmodel->insertBatch($equipmentData);
                }
            }


            return redirect()->to(admin_url('enterprises'))->with('message', 'Enterprise Establishment Data Added Successfully');
        }

        return $this->getForm();
    }

    public function edit()
    {
        $entequipmentmodel = new EnterprisesEquipmentModel();

        $enterprisesmodel = new EnterprisesModel();
        if ($this->request->getMethod(1) == 'POST') {

            $id = $this->request->getGet('id');
            $district_id = $this->request->getPost('district_id');
            $block_id = $this->request->getPost('block_id');
            $enterprisesmodel->where('id', $id)->delete();
            //if is_support_basis_infr is no set values
            if ($this->request->getPost('is_support_basis_infr') == 0) {
                $data = [
                    'purpose_infr_support' => '',
                    'support_infr_amount' => '',
                    'addl_budget' => '',
                ];
            } else {

                $data = [
                    'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
                    'support_infr_amount' => $this->request->getPost('support_infr_amount'),
                    'addl_budget' => $this->request->getPost('addl_budget'),
                ];
            }


            $enterprisesdata = [
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
                'addl_budget' => $data['addl_budget'],
                'unit_budget_amount' => $this->request->getPost('unit_budget_amount'),
                'is_support_basis_infr' => (int)$this->request->getPost('is_support_basis_infr'),
                'purpose_infr_support' => $data['purpose_infr_support'],
                'support_infr_amount' => $data['support_infr_amount'],
                'own_share' => $this->request->getPost('own_share'),
                'center_type' => $this->request->getPost('center_type'),
                'main_center_id' => $this->request->getPost('main_center_id'),

            ];
            // dd($enterprisesdata);
            $data['enterprises'] = $enterprisesmodel->insert($enterprisesdata);
            $unitId = $enterprisesmodel->insertID();
            $id = $this->request->getGet('id');

            $entequipmentmodel->where('id', $id)->delete();
            $equipmentData = [];

            if ($unitId) {

                $equipments = $this->request->getPost('equipments');
                $quantities = $this->request->getPost('quantity');

                // Check if equipment details are available and they are arrays
                if ($equipments && $quantities && is_array($equipments) && is_array($quantities)) {
                    // Loop through equipment details and quantities
                    foreach ($equipments as $index => $equipment) {
                        // Use the same index for both equipments and quantities arrays
                        $quantity = $quantities[$index] ?? 0;

                        $equipmentData[] = [
                            'ent_id' => $unitId,
                            'equipment_id' => $equipment,
                            'quantity' => $quantity,
                        ];
                    }
                    $data['enterprises'] = $entequipmentmodel->insertBatch($equipmentData);
                }
            }

            return redirect()->to(admin_url('enterprises'))->with('message', 'Enterprise Establishment Data Updated Successfully');
        }


        return $this->getForm();
    }

    public function ajaxBlocks()
    {

        $data['blocks'] = [];
        $BlocksModel = new BlockModel();
        // $blockModel = new LgdBlocksModel();

        $district_id = $this->request->getGet('district_id');

        $data['blocks'] = $BlocksModel->where('district_id', $district_id)->orderBy('name', 'asc')->findAll();
        // printr($data);exit;
        return $this->response->setJSON($data);
    }

    public function ajaxDoe()
    {
        $data['years'] = [];
        $enterprisesmodel = new EnterprisesModel();
        $district_id = $this->request->getGet('district_id');
        $data['years'] = $enterprisesmodel->yearWise($district_id);

        return $this->response->setJSON($data);
    }

    public function ajaxgps()
    {
        $data['gps'] = [];
        $gpmodel = new EnterpriseGpModel();

        $block_id = $this->request->getGet('block_id');

        $data['gps'] = $gpmodel->where('block_id', $block_id)->orderBy('name', 'asc')->findAll();

        return $this->response->setJSON($data);
    }

    public function ajaxvillages()
    {
        $data['villages'] = [];
        $villagemodel = new EnterpriseVillagesModel();
        // $lgdvillagemodel = new LgdVillagesModel();
        $gp_id = $this->request->getGet('gp_id');

        $data['villages'] = $villagemodel->where('gp_id', $gp_id)
            ->orderBy('name', 'asc')->findAll();

        return $this->response->setJSON($data);
    }

    public function ajaxcenter()
    {

        $enterprisesmodel = new EnterprisesModel();
        $enterprisesunitmodel = new EnterprisesUnitModel();

        $data['main_center_name'] = [];
        $district_id = $this->request->getGet('district_id');
        $block_id = $this->request->getGet('block_id');
        $unit_id = $this->request->getGet('unit_id');

        $mainCenters = $enterprisesmodel->getMainCenters($district_id, $block_id, $unit_id);

        $data['main_centers'] = $mainCenters;

        $data['message'] = "";
        if (empty($mainCenters)) {
            $data['message'] = "No Main Center Found";
        }

        return $this->response->setJSON($data);
    }


    public function getForm()
    {
        $this->template->add_package(['uploader', 'jquery_loading', 'select2'], true);
        // $this->template->add_package(array('datatable','select2'),true);
        helper('form');
        $enterprisesmodel = new EnterprisesModel();
        $enterprisesbudgetmodel = new EnterprisesBudgetModel();
        $enterprisesunitmodel = new EnterprisesUnitModel();
        $villagemodel = new EnterpriseVillagesModel();
        $gpmodel = new EnterpriseGpModel();
        $districtmodel = new DistrictModel();
        $blockmodel = new BlockModel();

        $equipmentmodel = new EquipmentModel();

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        $data['enterprise_text'] = "Add Enterprise Data";
        // dd($this->request->getGet('id'));
        if ($this->request->getGet('id') && ($this->request->getMethod(true) != 'POST')) {
            $enterprise = $enterprisesmodel->find($this->request->getGet('id'));

            $data['enterprise_text'] = "Edit Enterprise Data";
        }

        // dd($enterprisesmodel->db->getFieldNames('enterprises'));
        foreach ($enterprisesmodel->db->getFieldNames('enterprises') as $field) {
            if ($this->request->getPost($field)) {

                $data[$field] = $this->request->getPost($field);
            } else if (isset($enterprise->{$field}) && $enterprise->{$field}) {
                $data[$field] = html_entity_decode($enterprise->{$field}, ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }
        // dd($data);
        //
        $id = $this->request->getGet('id');
        $data['enterpriseequipments'] = [];
        if ($this->request->getGet('id') && ($this->request->getMethod(true) != 'POST')) {
            $data['enterpriseequipments'] = $enterprisesmodel->equipment($id);
        }

        //units
        $data['units'] = [];
        $data['units'][] = 'Select Units';

        $units = $enterprisesunitmodel->findAll();

        foreach ($units as $unit) {
            $data['units'][$unit->id] = $unit->name;
        }

        //management_unit_types
        $data['management_unit_types'] = [
            'SHG' => 'SHG',
            'FPO' => 'FPO',
        ];

        //saraswatee
        //district

        $districtmodel = new DistrictModel();
        $data['districts'][0] = 'Select Districts';

        if ($this->user->district_id) {
            $districts = $districtmodel->where('id', $this->user->district_id)->asArray()->orderBy('name', 'asc')->findAll();
        } else {
            $districts = $districtmodel->orderBy('name', 'asc')->findAll();
        }
        
        $data['district_id'] = $this->user->district_id;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }

        //rakesh s
        // $data['districts'][] = 'Select districts';
        // $district_id = 0;
        // $districts = $districtmodel->asArray()->orderBy('name', 'asc')->findAll();
        $data['districts'] = $districts;


        $data['blocks'][0] = 'Select Block';

        if ($this->request->getGet('id') && ($this->request->getMethod(true) != 'POST')) {
            $enterprise = $enterprisesmodel->find($id);
            $district_id = $enterprise->district_id;
            $block_id = $enterprise->block_id;
            $gp_id = $enterprise->gp_id;

            $blocks = $blockmodel->where('district_id', $district_id)->findAll();

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
            $gps = $gpmodel->where('block_id', $data['block_id'])->findAll();

            foreach ($gps as $gp) {
                $data['gps'][$gp->id] = $gp->name;
            }
            $villages = $villagemodel->where('gp_id', $data['gp_id'])->findAll();

            foreach ($villages as $village) {
                $data['villages'][$village->id] = $village->name;
            }
        }

        $data['gps'][] = 'Select Gp';

        $data['villages'][] = 'Select Village';

        //equipment

        $data['equipments'][] = 'Select Equipment name';

        $equipments = $equipmentmodel->findAll();

        foreach ($equipments as $equipment) {
            $data['equipments'][$equipment->id] = $equipment->name;
        }
        // $equipment_id = 0;
        $data['equipment_id'] = 0;
        //Budget fin yrs
        $data['unit_budgets'][] = 'Select budgets';
        // $budget_id = [];
        $unit_budgets = $enterprisesbudgetmodel->findAll();

        foreach ($unit_budgets as $unit_budget) {
            $data['unit_budgets'][$unit_budget->id] = $unit_budget->budget_code;
        }
        //Addl budget
        $data['addl_budgets'][] = 'Select budgets';

        $addl_budgets = $enterprisesbudgetmodel->findAll();

        foreach ($addl_budgets as $addl_budget) {
            $data['addl_budgets'][$addl_budget->id] = $addl_budget->budget_code;
        }
        ////Budget final year
        $yearmodel = new YearModel();
        $data['budget_fin_yrs'][] = 'Select Budget Year';

        $budget_fin_yrs = $yearmodel->findAll();

        foreach ($budget_fin_yrs as $budget_fin_yr) {
            $data['budget_fin_yrs'][$budget_fin_yr->id] = $budget_fin_yr->name;
        }
        //main center dropdown
        $data['main_center_name'][] = 'Select main center name';

        $data['add_gp_url'] = admin_url('grampanchayat/add');

        //Add village Url
        $data['add_village_url'] = admin_url('village/add');

        return $this->template->view('Admin\Enterprises\Views\addEstablishment', $data);
    }

    public function getLgdGps()
    {
        $block_id = $this->request->getGet('block_id');
        $gpModel = new LgdGpsModel();

        $block = (new LgdBlocksModel())->where('block_id', $block_id)->first();

        $data['gps'] = $gpModel->where('block_lgd_code', $block->lgd_code)
            ->orderBy('name', 'asc')->asArray()->findAll();

        $data['label'] = 'Select Gp';
        $data['id'] = 'gp_id';
        $data['gp_id'] = '';
        $data['block_id'] = $block_id;

        $data['post_url'] = admin_url('grampanchayat/ajaxadd');
        $data['title'] = 'Add new Gram panchayat';
        $data['html'] = view('Admin\Enterprises\Views\modal_dropdown', $data);

        return $this->response->setJSON($data);
    }

    public function getLgdVillages()
    {
        $gp_id = $this->request->getGet('gp_id');
        $vModel = new LgdVillagesModel();

        $gp = (new GrampanchayatModel())->find($gp_id);

        $data['gps'] = $vModel->where('gp_lgd_code', $gp->lgd_code)
            ->orderBy('name', 'asc')->asArray()->findAll();

        $data['label'] = 'Select Village';
        $data['id'] = 'village_id';
        $data['gp_id'] = $gp_id;
        $data['block_id'] = $gp->block_id;

        $data['post_url'] = admin_url('village/ajaxadd');
        $data['title'] = 'Add new Village';
        $data['html'] = view('Admin\Enterprises\Views\modal_dropdown', $data);

        return $this->response->setJSON($data);
    }
}
