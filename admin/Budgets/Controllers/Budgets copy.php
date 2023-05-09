<?php 
namespace Admin\Budgets\Controllers;

use Admin\Budgets\Models\BudgetModel;
use Admin\Budgets\Models\BudgetPlanModel;
use Admin\Common\Models\CommonModel;
use Admin\Common\Models\YearModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;

class Budgets extends AdminController {
    use TreeTrait;
    private $error = array();

    function __construct(){
        $this->budgetPlanModel=new BudgetPlanModel();
        $this->budgetModel=new BudgetModel();
    }
    public function index() {
        $this->template->set_meta_title('Budgets Plan');
        return $this->getList();
    }

    protected function getList() {

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'Budgets',
            'href' => admin_url('budgets')
        );

        $this->template->add_package(array('datatable'),true);

        $data['add'] = admin_url('budgets/add');
        $data['delete'] = admin_url('budgets/delete');
        $data['datatable_url'] = admin_url('budgets/search');

        $data['heading_title'] = 'Budgets Plan';

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        return $this->template->view('Admin\Budgets\Views\budgetPlan', $data);
    }

    public function search() {
        $requestData= $_REQUEST;
        $totalData = $this->budgetPlanModel->getTotal();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'order'  		 => $requestData['order'][0]['dir'],
            'sort' 			 => $requestData['order'][0]['column'],
            'start' 			 => $requestData['start'],
            'limit' 			 => $requestData['length']
        );
        $totalFiltered = $this->budgetPlanModel->getTotal($filter_data);

        $filteredData = $this->budgetPlanModel->getAll($filter_data);
        //printr($filteredData);
        $years=(new YearModel())->asArray()->findAll();

        $datatable=array();
        foreach($filteredData as $result) {
            $ykey=array_search($result->year,array_column($years,'id'));

            $action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-warning" href="'.admin_url('budgets/details/'.$result->id).'"><i class="fa fa-list"></i></a>';
            $action .= 		'<a class="btn btn-sm btn-info" href="'.admin_url('budgets/view/'.$result->id).'"><i class="fa fa-eye"></i></a>';
            $action .= 		'<a class="btn btn-sm btn-primary ajaxaction" href="'.admin_url('budgets/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
            $action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('budgets/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[]=array(
                '<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
                $result->fund_agency,
                $years[$ykey]['name'],
                $result->district,
                $result->block,
                $action
            );

        }
        //printr($datatable);
        $json_data = array(
            "draw"            => isset($requestData['draw']) ? intval( $requestData['draw'] ):1,
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $datatable
        );
        return $this->response->setContentType('application/json')
            ->setJSON($json_data);

    }

    public function add(){
        $this->template->set_meta_title("Budgets Plan");

        if ($this->request->getMethod(1) === 'POST' ){

            $id=$this->budgetPlanModel->insert($this->request->getPost());
            $this->session->setFlashdata('message', 'Budget Plan Saved Successfully.');

            return redirect()->to(admin_url('budgets'));


        }
        $this->getForm();
    }

    public function edit(){
        $this->template->set_meta_title("Budget Plan");

        if ($this->request->getMethod(1) === 'POST'){
            $id=$this->uri->getSegment(4);

            $this->budgetPlanModel->update($id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Budget Plan Updated Successfully.');

            return redirect()->to(admin_url('budgets'));

        }
        $this->getForm();
    }

    protected function getForm(){

        $this->template->add_package(array('select2'),true);


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => "Budgets",
            'href' => admin_url('budgets')
        );

        $data['heading_title'] 	= 'Budgets Plan';
        $data['text_form'] = $this->uri->getSegment(4) ? "Budget Plan Edit" : "Budget Plan Add";
        $data['cancel'] = admin_url('budgets');

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $budgetplan_info = $this->budgetPlanModel->find($this->uri->getSegment(4));

        }

        foreach($this->budgetPlanModel->getFieldNames('soe_budgets_plan') as $field) {
            if($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if(isset($budgetplan_info->{$field}) && $budgetplan_info->{$field}) {
                $data[$field] = html_entity_decode($budgetplan_info->{$field},ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }



        $data['fund_agencies'] = (new BlockModel())->getFundAgencies();
        //printr($data['fund_agencies']);
        $data['block_phases']=(new BlockModel())->getTotalPhaseByAgency(1);
        $data['years']=(new YearModel())->findAll();
        $data['districts']=(new DistrictModel())->findAll();

        echo $this->template->view('Admin\Budgets\Views\budgetPlanForm',$data);
    }

    protected function validateForm() {

        $validation =  \Config\Services::validation();
        $id=$this->uri->getSegment(5);

        $rules = $this->budgetPlanModel->validationRules;

        if ($this->validate($rules)){
            return true;
        }
        else{
            //printr($validation->getErrors());
            $this->error['warning']="Warning: Please check the form carefully for errors!";
            return false;
        }
        return !$this->error;
    }
    public function details(){
        $data=[];
        $data['heading_title'] 	= "Budgets Details";

        if ($this->request->getMethod(1) === 'POST'){
            $budget_plan_id=$this->uri->getSegment(4);
            //printr($this->request->getPost());
            //exit;
            $this->budgetModel->editBudget($budget_plan_id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Budget Updated Successfully.');

            return redirect()->to(admin_url('budgets/details/'.$budget_plan_id));

        }

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $budgetplan_info = $this->budgetPlanModel->getBudgetPlan($this->uri->getSegment(4));
            $agencyphase=(new BlockModel())->getTotalPhaseByAgency($budgetplan_info->fund_agency_id);
            
            $data['text_form'] = "Budget Details for ";

        }
        $agency_types=(new CommonModel())->getAgencyTypes();


        if($budgetplan_info){
            $pmubudgetplan=[
                'phase_no'=>0,
                'name'=>'PMU/Add/Procurement Component',
                'category'=>['pmu', 'addl', 'procurement']
            ];
            array_push($agencyphase,$pmubudgetplan);
            //printr($agencyphase);
            //exit;
            $data['agencyphase']=$agencyphase;
            foreach($agencyphase as $key=>$budgetplan){
                $filter=[
                    'budget_plan_id'=>$budgetplan_info->id,
                    'fund_agency_id'=>$budgetplan_info->fund_agency_id,
                    'phase'=>$budgetplan['phase_no'],
                    'year'=>$budgetplan_info->year,
                    'category'=>isset($budgetplan['category'])?$budgetplan['category']:['program']
                ];

                $components = $this->budgetModel->getBudgetDetails($filter);
                //$components=[];

                if($components) {
                    $components = $this->buildTree($components);
                    $data['components'][$key]['phase'] = $budgetplan['phase_no'];
                    $data['components'][$key]['year'] = $budgetplan_info->year;
                    $data['components'][$key]['fund_agency_id'] = $budgetplan_info->fund_agency_id;
                    //$data['components'][$key]['budgets']=$components;
                    $data['components'][$key]['budgets'] = $this->getTable($components,$agency_types,$key);
                }
            }

            return $this->template->view('Admin\Budgets\Views\budgetForm', $data);

        }else{

        }
    }

    public function view(){
        $data=[];
        $data['heading_title'] 	= "Budgets View";

        $budget_plan_id=$this->uri->getSegment(4);

        $budgetplan_info = $this->budgetPlanModel->find($budget_plan_id);
        $fundagency=(new CommonModel())->getFundAgency($budgetplan_info->fund_agency_id);
        $agencyphase=(new BlockModel())->getTotalPhaseByAgency($budgetplan_info->fund_agency_id);

        $data['text_form'] = "Budget Details for ";



        $agency_types=(new CommonModel())->getAgencyTypes();


        if($budgetplan_info){
            $data['budget_plan_id']=$budgetplan_info->id;
            $data['fund_agency_id']=$budgetplan_info->fund_agency_id;
            $data['phase']=$budgetplan_info->phase;
            $data['year']=$budgetplan_info->year;
            $filter=[
                'budget_plan_id'=>$budgetplan_info->id,
                'fund_agency_id'=>$budgetplan_info->fund_agency_id,
                'phase'=>$budgetplan_info->phase,
                'year'=>$budgetplan_info->year
            ];
            $components = $this->budgetModel->getBudgetDetails($filter);

            $data['components'] = '';
            if($components) {
                $components = $this->buildTree($components);
               //printr($components);
                //exit;
                $data['components'] = $this->getCTable($components,$agency_types);
            }


            return $this->template->view('Admin\Budgets\Views\budgetForm', $data);

        }
    }

    private function getTable($array,$agency_types,$key) {
        $html = '';

        foreach ($array as $item) {
            if($item['row_type']=='heading') {
                $html .= '<tr>
                    <th>' . $item['number'] . '</th>
                    <th colspan="6">' . $item['description'] . '</th>
                ';
            } else {
                $html .= '<tr>
                    <td>
                    <input type="hidden" class="form-control" name="phase['.$key.'][budget]['.$item['component_id'].'][component_id]" value="'.$item['component_id'].'">
                    <input type="hidden" class="form-control" name="phase['.$key.'][budget]['.$item['component_id'].'][category]" value="'.$item['category'].'">
                    <label for="cb' . $item['id'] . '">' . $item['number'] . '</label></td>
                    <td><label for="cb' . $item['component_id'] . '">' . $item['description'] . '</label></td>
                    <td>
                    <select class="form-control" name="phase['.$key.'][budget]['.$item['component_id'].'][agency_type_id]">';
                    $html .= '<option value="0">Select Agency</option>';

                    foreach($agency_types as $agency_type) {
                            if ($agency_type->id == $item['agency_type_id']) {
                                $html .= '<option value=' . $agency_type->id . ' selected="selected">' . $agency_type->name . '</option>';
                            } else {
                                $html .= '<option value=' . $agency_type->id . '>' . $agency_type->name . '</option>';
                            }
                        }
                    $html .= '</select>
                    </td>
                    <td><input type="text" class="form-control" name="phase['.$key.'][budget]['.$item['component_id'].'][units]" value="'.$item['units'].'"></td></td>
                    <td><input type="text" class="form-control rate" name="phase['.$key.'][budget]['.$item['component_id'].'][unit_cost]" value="'.$item['unit_cost'].'"></td>
                    <td><input type="text" class="form-control physical" name="phase['.$key.'][budget]['.$item['component_id'].'][physical]" value="'.$item['physical'].'"></td>
                    <td><input type="text" class="form-control financial" name="phase['.$key.'][budget]['.$item['component_id'].'][financial]" value="'.$item['financial'].'"></td>
                    ';
            }
            if (!empty($item['children'])){
                $html .= $this->getTable($item['children'],$agency_types,$key);
            }
            $html .= '</tr>';
        }

        return $html;

    }
}
