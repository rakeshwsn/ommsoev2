<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\BlockModel;
use Admin\Enterprises\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesBudgetModel;
use Admin\Enterprises\Models\EnterprisesEquipmentModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\EnterpriseGpModel;
use Admin\Enterprises\Models\EnterpriseVillagesModel;
use Admin\Enterprises\Models\YearModel;
use Admin\Equipment\Models\EquipmentModel;
use Admin\Localisation\Models\LgdGpsModel;
use Admin\Localisation\Models\LgdVillagesModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EnterprisesMaster extends AdminController
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
        $this->districtModel = new \Admin\Dashboard\Models\DistrictModel();
        $this->blockModel = new \Admin\Dashboard\Models\BlockModel();
        $this->enterprisesunitmodel = new EnterprisesUnitModel();
        $this->lgdGpModel = new LgdGpsModel();
        $this->entEquipmentModel = new EnterprisesEquipmentModel();
        $this->gpModel = new EnterpriseGpModel();
        $this->villageModel = new EnterpriseVillagesModel();
        $this->enterprisesBudgetModel = new EnterprisesBudgetModel();
        $this->yearModel = new \Admin\Dashboard\Models\YearModel();
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

}
