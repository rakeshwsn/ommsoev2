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
    private $enterprisesModel;
    private $districtModel;
    private $blockModel;
    private $enterprisesunitmodel;
    private $lgdGpModel;
    private $entEquipmentModel;
    private $gpModel;
    private $villageModel;
    private $enterprisesBudgetModel;
    private $yearModel;
    private $lgdVillageModel;
    private $equipmentModel;

    public function __construct()
    {
        $this->enterprisesModel = new EnterprisesModel();
        $this->districtModel = new DistrictModel();
        $this->blockModel = new BlockModel();
        $this->enterprisesunitmodel = new EnterprisesUnitModel();
        $this->lgdGpModel = new LgdGpsModel();
        $this->entEquipmentModel = new EnterprisesEquipmentModel();
        $this->gpModel = new EnterpriseGpModel();
        $this->villageModel = new EnterpriseVillagesModel();
        $this->enterprisesBudgetModel = new EnterprisesBudgetModel();
        $this->yearModel = new YearModel();
        $this->lgdVillageModel = new LgdVillagesModel();
        $this->equipmentModel = new EquipmentModel();
    }
    public function index()
    {
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
        $this->template->set('header', true);
        helper('form');

        //populate districts
        $data['districts'][0] = 'Select Districts';
        // If user district_id avaliable

        if ($this->user->district_id) {
            $districts =  $this->districtModel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
        } else {
            $districts =  $this->districtModel->orderBy('name', 'asc')->findAll();
        }
        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }
        $data['district_id'] = $this->user->district_id;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }

        /// Units populate

        $data['units'][0] = 'Select Unit Type';
        $units = $this->enterprisesunitmodel->orderBy('name', 'asc')->findAll();
        foreach ($units as $unit) {
            $data['units'][$unit->id] = $unit->name;
        }
        $data['unit_id'] = 0;

        //populate blocks of district selected

        $data['blocks'][0] = 'Select Block';
        // if user block_id is avaliable populate blocks else populate selected district's blocks
        if ($this->user->block_id) {
            $blocks =  $this->blockModel->where('id', $this->user->block_id)->orderBy('name', 'asc')->findAll();
            $data['block_id'] = $this->user->block_id;

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        } else {
            $blocks =  $this->blockModel->where('district_id', $data['district_id'])->orderBy('name', 'asc')->findAll();
            $data['block_id'] = $this->request->getGet('block_id');

            foreach ($blocks as $block) {
                $data['blocks'][$block->id] = $block->name;
            }
        }
        //if user district_id is avaliable populate years
        $data['years'][0] = 'Select DOE';

        if ($this->user->district_id) {
            $years =  $this->enterprisesModel->yearWise($this->user->district_id);
            $data['district_id'] = $this->user->district_id;


            foreach ($years as $year) {
                $data['years'][$year->year] = $year->year;
            }
        } elseif($this->request->getGet('district_id')) {
            $district_id = $this->request->getGet('district_id');
            $years =  $this->enterprisesModel->where('district_id', $district_id)->yearWise($district_id);
            $data['district_id'] = $this->request->getGet('district_id');

            foreach ($years as $year) {
                $data['years'][$year->year] = $year->year;
            }
        }


        //get management unit type
        $data['management_unit_type'] = '';
        if ($this->request->getGet('management_unit_type')) {
            $data['management_unit_type'] = $this->request->getGet('management_unit_type');
        }

        // get doeyear
        $data['doeyear'] = 0;
        if ($this->request->getGet('doeyear')) {
            $data['doeyear'] = $this->request->getGet('doeyear');
        }

        // Unit_id populate
        $data['unit_id'] = 0;
        if ($this->request->getGet('unit_id')) {
            $data['unit_id'] = $this->request->getGet('unit_id');
        }


        $data['enterprises'] = [];

        $data['edit_url'] = admin_url('enterprises/edit');
        $data['datatable_url'] = admin_url('enterprises/search');

        $data['excel_link'] = admin_url('enterprises/exceldownld');
// dd($data);
        return $this->template->view('Admin\Enterprises\Views\enterprise_index', $data);
    }

    public function search()
    {

        $requestData = $_REQUEST;
        $totalData = $this->enterprisesModel->getTotals();
        $totalFiltered = $totalData;
        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'district_id' => $requestData['district_id'],
            'block_id' => $requestData['block_id'],
            'unit_id' => $requestData['unit_id'],
            'doeyear' => $requestData['year'],
            'management_unit_type' => $requestData['management_unit_type'],
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length'],
        );

        $totalFiltered = $this->enterprisesModel->getTotals($filter_data);

        $filteredData = $this->enterprisesModel->getAll($filter_data);

        $datatable = array();
        foreach ($filteredData as $result) {

            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .=         '<a class="btn btn-sm btn-primary" href="' . admin_url('enterprises/edit?id=' . $result->id) . '"><i class="fa fa-pencil"></i></a>';

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
                $result->created_at,
                $action,
            );
        }

        $json_data = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        );

        return $this->response->setContentType('application/json')->setJSON($json_data);
    }

    private function filter()
    {
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
        if ($this->user->block_id) {
            $filter['block_id'] = $this->user->block_id;
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
        $filteredData =  $this->enterprisesModel->getTotals($filter);

        return $filteredData;
    }
    public function download()
    {

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

            $this->enterprisesModel->where('id', $this->request->getGet('id'))->delete();
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
                'address' => $this->request->getPost('address'),


            ];
            $this->enterprisesModel->insert($enterprisesdata);
            $unitId =  $this->enterprisesModel->insertID();
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
                    $this->entEquipmentModel->insertBatch($equipmentData);
                }
            }
            return redirect()->to(admin_url('enterprises'))->with('message', 'Enterprise Establishment Data Added Successfully');
        }
        return $this->getForm();
    }

    public function edit()
    {

        if ($this->request->getMethod(1) == 'POST') {
            $id = $this->request->getGet('id');
            $district_id = $this->request->getPost('district_id');
            $block_id = $this->request->getPost('block_id');
            $this->enterprisesModel->where('id', $id)->delete();
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
                'address' => $this->request->getPost('address'),

            ];
            // dd($enterprisesdata);
            $data['enterprises'] =  $this->enterprisesModel->insert($enterprisesdata);
            $unitId =  $this->enterprisesModel->insertID();
            $id = $this->request->getGet('id');

            $this->entEquipmentModel->where('id', $id)->delete();
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
                    $data['enterprises'] = $this->entEquipmentModel->insertBatch($equipmentData);
                }
            }
            return redirect()->to(admin_url('enterprises'))->with('message', 'Enterprise Establishment Data Updated Successfully');
        }


        return $this->getForm();
    }

    public function ajaxBlocks()
    {
        $data['blocks'] = [];
        if ($this->user->block_id) {
            $block_id = $this->user->block_id;
            $data['blocks'] = $this->blockModel->where('id', $block_id)->orderBy('name', 'asc')->findAll();
        } else {
            $data['blocks'] = $this->blockModel->where('district_id', $this->request->getGet('district_id'))->orderBy('name', 'asc')->findAll();
        }
        return $this->response->setJSON($data);
    }

    public function ajaxDoe()
    {
        $data['years'] = [];
        $district_id = $this->request->getGet('district_id');
        $data['years'] =  $this->enterprisesModel->yearWise($district_id);
        return $this->response->setJSON($data);
    }

    public function ajaxgps()
    {
        $data['gps'] = [];

        $block_id = $this->request->getGet('block_id');

        $data['gps'] = $this->gpModel->where('block_id', $block_id)->orderBy('name', 'asc')->findAll();

        return $this->response->setJSON($data);
    }

    public function ajaxvillages()
    {
        $data['villages'] = [];
        $gp_id = $this->request->getGet('gp_id');

        $data['villages'] = $this->villageModel->where('gp_id', $gp_id)->orderBy('name', 'asc')->findAll();

        return $this->response->setJSON($data);
    }

    public function ajaxcenter()
    {

        $data['main_centers'] = [];
        $data['main_center_name'] = [];
        $district_id = $this->request->getGet('district_id');
        $block_id = $this->request->getGet('block_id');
        $unit_id = $this->request->getGet('unit_id');

        $mainCenters =  $this->enterprisesModel->getMainCenters($district_id, $unit_id, $block_id);

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
        helper('form');

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }
        //enterprise text name change when id avaliable
        $data['enterprise_text'] = "Add Enterprise Data";
        if ($this->request->getGet('id') && ($this->request->getMethod(true) != 'POST')) {
            $enterprise =  $this->enterprisesModel->find($this->request->getGet('id'));
            $data['enterprise_text'] = "Edit Enterprise Data";
        }
        // for view or edit
        foreach ($this->enterprisesModel->db->getFieldNames('enterprises') as $field) {
            //add fields for edit
            if ($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if (isset($enterprise->{$field}) && $enterprise->{$field}) {
                // view or edit
                $data[$field] = html_entity_decode($enterprise->{$field}, ENT_QUOTES, 'UTF-8');
            } else {
                //add fields for add
                $data[$field] = '';
            }
        }

        $id = $this->request->getGet('id');
        $data['enterpriseequipments'] = [];
        //for edit or view data
        if ($this->request->getGet('id') && ($this->request->getMethod(true) != 'POST')) {
            $data['enterpriseequipments'] =  $this->enterprisesModel->equipment($id);
        }

        //units
        $data['units'] = [];
        $data['units'][] = 'Select Units';

        $units = $this->enterprisesunitmodel->findAll();

        foreach ($units as $unit) {
            $data['units'][$unit->id] = $unit->name;
        }

        //management_unit_types
        $data['management_unit_types'] = [
            'SHG' => 'SHG',
            'FPO' => 'FPO',
        ];

        // initialise district
        $data['districts'] = [];
        //district populate when user district_id avaliable else all districts populate
        if ($this->user->district_id) {
            $districts = $this->districtModel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
            $data['district_id'] = $this->user->district_id;
        } else {
            $districts = $this->districtModel->orderBy('name', 'asc')->findAll();
        }
        // store districts in array for select
        $data['districts'] = $districts;

        //initialise block
        $data['blocks'] = [];
        $data['gps'] = [];

        //when district id avaliable show blocks otherwise all blocks populate
        if ($this->user->block_id) {
            $blocks  = $this->blockModel->where('id', $this->user->block_id)->orderBy('name', 'asc')->findAll();

            $data['blocks'] = $blocks;

            $data['block_id'] = $this->user->block_id;
            //get gps of the block of fa level
            $gps = $this->gpModel->where('block_id', $this->user->block_id)->findAll();
            $data['gps'] = $gps;
        } else if ($this->user->district_id) {
            $blocks = $this->blockModel->where('district_id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
            $data['blocks'] = $blocks;
        }

        $data['villages'] = [];

        //get data for edit
        if ($this->request->getGet('id') && ($this->request->getMethod(true) != 'POST')) {
            $enterprise =  $this->enterprisesModel->find($id);
            $district_id = $enterprise->district_id;
            $block_id = $enterprise->block_id;
            $gp_id = $enterprise->gp_id;

            $blocks = $this->blockModel->where('district_id', $district_id)->findAll();

            $data['blocks'] = $blocks;

            $gps = $this->gpModel->where('block_id', $block_id)->findAll();

            $data['gps'] = $gps;

            $villages = $this->villageModel->where('gp_id', $gp_id)->findAll();

            $data['villages'] = $villages;
        }

        //equipment list

        $data['equipments'][] = 'Select Equipment name';

        $equipments = $this->equipmentModel->findAll();

        foreach ($equipments as $equipment) {
            $data['equipments'][$equipment->id] = $equipment->name;
        }

        $data['equipment_id'] = 0;
        //Budget fin yrs
        $data['unit_budgets'][] = 'Select budgets';
        $unit_budgets = $this->enterprisesBudgetModel->findAll();

        foreach ($unit_budgets as $unit_budget) {
            $data['unit_budgets'][$unit_budget->id] = $unit_budget->budget_code;
        }
        //Addl budget
        $data['addl_budgets'][] = 'Select budgets';

        $addl_budgets = $this->enterprisesBudgetModel->findAll();

        foreach ($addl_budgets as $addl_budget) {
            $data['addl_budgets'][$addl_budget->id] = $addl_budget->budget_code;
        }
        ////Budget final year
        $data['budget_fin_yrs'][] = 'Select Budget Year';

        $budget_fin_yrs = $this->yearModel->findAll();

        foreach ($budget_fin_yrs as $budget_fin_yr) {
            $data['budget_fin_yrs'][$budget_fin_yr->id] = $budget_fin_yr->name;
        }
        //main center dropdown when anyone select sub center
        $data['main_centers'] = [];
        // main center will be shown only when sub center is selected and have district, block and unit id
        $filter = [
            'center_type' => 'main_center',
            'district_id' => $data['district_id'],
            'block_id' => $data['block_id'],
            'unit_id' => $data['unit_id'],
        ];
        // main center name is concatenated with management unit type and management unit name and its id will same as enterprise id
        $main_centers =  $this->enterprisesModel->select("id, CONCAT(managing_unit_name,' (',management_unit_type,')') as name")
            ->where($filter)->findAll();

        $data['main_centers'] = $main_centers;
        // add gp url
        $data['add_gp_url'] = admin_url('grampanchayat/add');

        //Add village Url
        $data['add_village_url'] = admin_url('village/add');

        return $this->template->view('Admin\Enterprises\Views\addEstablishment', $data);
    }

    public function getLgdGps()
    {
        $block_id = $this->request->getGet('block_id');
        // get all LGD gps of the block selected
        $block = (new LgdBlocksModel())->where('block_id', $block_id)->first();

        $data['gps'] = $this->lgdGpModel->where('block_lgd_code', $block->lgd_code)->orderBy('name', 'asc')->asArray()->findAll();
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

        $gp = (new GrampanchayatModel())->find($gp_id);

        $data['gps'] = $this->lgdVillageModel->where('gp_lgd_code', $gp->lgd_code)
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
