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
use Config\Url;

class Budgets extends AdminController {
    use TreeTrait;
    private $error = array();
    private $budgetModel;
    private $budgetPlanModel;
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

        $this->template->add_package(array('datatable','select2'),true);

        $data['add'] = admin_url('budgets/add');
        $data['delete'] = admin_url('budgets/delete');
        $data['datatable_url'] = admin_url('budgets/search');

        $data['bulkbudegt'] = admin_url('budgets/bulkbudget');

        $data['heading_title'] = 'Budgets Plan';

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        $data['districts']=(new DistrictModel())->getAll();
        //$data['years'] = getAllYears();
        $data['years'] = (new YearModel())->where('id',getCurrentYearId())->asArray()->findAll();
        $f_filter=['fund_agency_id'=>$this->user->fund_agency_id];
        if($this->user->agency_type_id!=$this->settings->district_user) {
            $f_filter=[]; 
            $data['active_fund_agency']="";
        }else{
            $data['active_fund_agency']=$this->user->fund_agency_id;
            if($data['active_fund_agency']){
                $data['fund_agency_id']=$data['active_fund_agency'];
            }
        }
        
        $data['fundagencies'] = (new BlockModel())->getFundAgencies($f_filter);
        
        $data['user_id']=$this->user->getId();
        $data['year']=getCurrentYearId();
        
        $data['active_district']=$this->user->district_id;
        if($data['active_district']){
            $data['district_id']=$data['active_district'];
        }
		$data['active_block']=$this->user->block_id;
        if($data['active_block']){
            $data['block_id']=$data['active_block'];
        }
        $data['fund_agency_id']=$this->user->fund_agency_id;
        $data['get_district_url'] = Url::getDistricts;
        return $this->template->view('Admin\Budgets\Views\budgetPlan', $data);
    }

    public function search() {
        $requestData= $_REQUEST;
        $totalData = $this->budgetPlanModel->getTotal();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'filter_year'       => $requestData['year'],
            'filter_district_id'   => $requestData['district_id'],
            'filter_block_id'      => $requestData['block_id'],
            'filter_fund_agency_id' => $requestData['fund_agency_id'],
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
            if(getCurrentYearId() == $years[$ykey]['id']){
                $action  = '<div class="btn-group btn-group-sm pull-right">';
                if ($this->user->hasPermission('budgets/view')) {
                    $action .= 		'<a class="btn btn-sm btn-warning" href="'.admin_url('budgets/view/'.$result->id).'"><i class="fa fa-list"></i></a>';
                }
                if ($this->user->hasPermission('budgets/edit')) {
                    $action .= 		'<a class="btn btn-sm btn-primary ajaxaction" href="'.admin_url('budgets/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
                }
                if ($this->user->hasPermission('budgets/delete')) {
                    $action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('budgets/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
                }
                $action .= '</div>';
            }else{
                $action  = '<div class="btn-group btn-group-sm pull-right">';
                if ($this->user->hasPermission('budgets/view')) {
                    $action .= 		'<a class="btn btn-sm btn-warning" href="'.admin_url('budgets/view/'.$result->id).'"><i class="fa fa-list"></i></a>';
                }
                $action .= '</div>';
            }
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
            $budeget_plan=$this->budgetPlanModel->getBudgetPlanByBlock($this->request->getPost());
            if($budeget_plan){
                $budget_plan_id=$budeget_plan->id;
                $this->session->setFlashdata('message', 'Budget Plan Already Exist.you can also fill up the budgets');
               
            }else{
                $budget_plan_id=$this->budgetPlanModel->insert($this->request->getPost());
                $this->session->setFlashdata('message', 'Budget Plan Saved Successfully.');

                return redirect()->to(admin_url('budgets'));
            }

            return redirect()->to(admin_url('budgets/view/'.$budget_plan_id));
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

        $data['active_district']=$this->user->district_id;
        if($data['active_district']){
            $data['district_id']=$data['active_district'];
        }

        echo $this->template->view('Admin\Budgets\Views\budgetPlanForm',$data);
    }

    public function bulkBudget(){
        $data=[];
        $data['heading_title'] 	= "Bulk Budgets Details";
        $this->template->add_package(array('select2'),true);
        
        if ($this->request->getMethod(1) === 'POST' ){
         
            $planpostdata=$this->request->getPost();

            if(!$this->request->getPost('block_id')){
                $planpostdata['block_id'][]=0;
            }
            
            foreach($planpostdata['block_id'] as $blockid){
                $plandata=[
                    'year'=>$this->request->getPost('year'),
                    'fund_agency_id'=>$this->request->getPost('fund_agency_id'),
                    'district_id'=>$this->request->getPost('district_id'),
                    'block_id'=>$blockid
                ];
                $budget_plan=$this->budgetPlanModel->getBudgetPlanByBlock($plandata);
                if($budget_plan){
                    $budget_plan_id=$budget_plan->id;
                }else{
                    $this->budgetPlanModel->insert($plandata);
                    $budget_plan_id=$this->budgetPlanModel->getInsertID();
                }

                $this->budgetModel->editBudget($budget_plan_id,$this->request->getPost());
            }
          
            $this->session->setFlashdata('message', 'Budget Updated Successfully.');

            return redirect()->to(admin_url('budgets'));

        }

        $data['details']=0;
        $data['components']=[];
        $data['fund_agency_id']=$fund_agency_id=$this->request->getGet('fund_agency_id');
        $data['year']=$year=$this->request->getGet('year');
        $data['district_id']=$district_id=$this->request->getGet('district_id');
        $data['block_id']=$block_id=$this->request->getGet('block_id');
        $agency_types=(new CommonModel())->getAgencyTypes();
        if ($this->request->getGet('fund_agency_id')){
           
            $data['details']=1;
            if($block_id){
                $phase=1;
                $category=['program'];
            }else if($district_id){
                $phase=0;
                $category=['pmu', 'addl', 'procurement','iyom'];
            }else{
                $phase=0;
                $category=['pmu', 'addl', 'procurement','iyom'];
            }

            $agency_type_id = 0;
            if($district_id==0 && empty($block_id)){
                $agency_type_id = 8;
            } else if($district_id!=0 && empty($block_id)){
                $agency_type_id = 7;
            } else if($district_id!=0 && !empty($block_id)){
                $agency_type_id = 5;
            }
            
            $filter=[
                'fund_agency_id'=>$fund_agency_id,
                'year'=>$year,
                'district_id'=>$district_id,
                'block_id'=>$block_id,
                'phase'=>$phase,
                'category'=>$category,
                'agency_type_id' => $agency_type_id
            ];

            

            $components = $this->budgetModel->getBulkBudgetDetails($filter);
            
            if($components) {
                $components = $this->buildTree($components);
                $data['components'][0]['year'] = $year;
                $data['components'][0]['phase'] = 0;
                $data['components'][0]['fund_agency_id'] = $fund_agency_id;
                $data['components'][0]['district_id'] = $district_id;
                $data['components'][0]['block_id'] = $block_id;
                //$data['components'][$key]['budgets']=$components;
                $data['components'][0]['budgets'] = $this->getBTable($components,'edit');
            }

        }
        
        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        $data['fund_agencies'] = (new BlockModel())->getFundAgencies(['fund_agency_id'=>$this->user->fund_agency_id]);
        $data['block_phases']=(new BlockModel())->getTotalPhaseByAgency(1);
        $data['years']=(new YearModel())->where('id',getCurrentYearId())->findAll();
        $data['districts']=(new DistrictModel())->findAll();

        $data['active_district']=$this->user->district_id;
        if($data['active_district']){
            $data['district_id']=$data['active_district'];
        }

        return $this->template->view('Admin\Budgets\Views\bulkBudgetForm', $data);
    }

    public function approval(){
        $this->template->add_package(array('datatable','select2'),true);

        $data['heading_title'] = 'Budgets Approval';

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        //$data['districts']=(new DistrictModel())->getAll();
        $data['years'] = getAllYears();
        //$data['fundagencies'] = (new BlockModel())->getFundAgencies(['fund_agency_id'=>$this->user->fund_agency_id]);
        $data['fundagencies'] = (new BlockModel())->getFundAgencies();
        $data['user_id']=$this->user->getId();
        
        $data['year']=getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year'] = $this->request->getGet('year');
        }
        
       
        $data['district_id'] = '';
        $data['active_district']=$this->user->block_id;
        if($data['active_district']){
            $data['district_id'] = $this->user->district_id;
        }

        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        }

		
        $data['fund_agency_id'] = 1;
        if($this->user->fund_agency_id){
            $data['fund_agency_id'] = $this->user->fund_agency_id;
        }
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        if($data['fund_agency_id']){
           $data['districts'] = (new DistrictModel())->getDistrictsByFundAgency($data['fund_agency_id']);
        }

        $data['get_district_url'] = Url::getDistricts;


        $filter_data = array(
            'filter_year'       => $data['year'],
            'filter_district_id'   => $data['district_id'],
            'filter_fund_agency_id' => $data['fund_agency_id']
        );
       
        $data['budgets'] = $this->budgetPlanModel->getCumulativeBudget($filter_data);
       
        foreach ($data['budgets'] as &$budget) {
            if($budget['status']==0){
                $budget['status'] = '<label class="badge badge-warning">'.$this->statuses[$budget['status']].'</label>';
            }
            if($budget['status']==1){
                $budget['status'] = '<label class="badge badge-success">'.$this->statuses[$budget['status']].'</label>';
            }
            if($budget['status']==2){
                $budget['status'] = '<label class="badge badge-danger">'.$this->statuses[$budget['status']].'</label>';
            }
            if($budget['status']==3){
                $budget['status'] = '<label class="badge badge-info">'.$this->statuses[$budget['status']].'</label>';
            }
           

            $budget['action'] = '';
            
            if($budget['budget_plan_id']){
                $budget['action'] = admin_url('budgets/details/'.$budget['budget_plan_id']);
            }
            
        }

        return $this->template->view('Admin\Budgets\Views\budgetApproval', $data);
    }

    public function details(){
        $data=[];
        $data['heading_title'] 	= "Budgets Details";

        $plan_id = $this->uri->getSegment(4);

        if($this->request->getMethod(1)=='POST'){

            $budgetplan_info = $this->budgetPlanModel->getBudgetPlan($this->uri->getSegment(4));
        
            $plan_data = [
                'status' => $this->request->getPost('status'),
                'status_user' => $this->user->user_id,
                'remarks' => $this->request->getPost('remarks')
            ];
            $conditions=[
                'year'=>$budgetplan_info->year,
                'fund_agency_id'=>$budgetplan_info->fund_agency_id,
                'district_id'=>$budgetplan_info->district_id
            ];
            
            $this->budgetPlanModel->approveBudget($plan_data,$conditions);

            $this->session->setFlashdata('message','Your changes have been saved.');
            return redirect()->to(current_url());
        }


        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $budgetplan_info = $this->budgetPlanModel->getBudgetPlan($this->uri->getSegment(4));
           // $agencyphase=(new BlockModel())->getTotalPhaseByAgency($budgetplan_info->fund_agency_id);
           
            $data['text_form'] = "Budget Details for ".$budgetplan_info->fund_agency."-".$budgetplan_info->yname."-".$budgetplan_info->district;

        }

        
        if($budgetplan_info){
            if($budgetplan_info->block_id){
                $phase=1;
                $category=['program'];
            }else if($budgetplan_info->district_id){
                $phase=0;
                $category=['pmu', 'addl', 'procurement','iyom'];
            }else{
                $phase=0;
                $category=['pmu', 'addl', 'procurement','iyom'];
            }
                
            
            $agency_type_id = 0;
            if($budgetplan_info->district_id==0 && $budgetplan_info->block_id==0){
                $agency_type_id = 8;
            } else if($budgetplan_info->district_id!=0 && $budgetplan_info->block_id==0){
                $agency_type_id = 7;
            } else if($budgetplan_info->district_id!=0 && $budgetplan_info->block_id!=0){
                $agency_type_id = 5;
            }
            $filter=[
                'budget_plan_id'=>$budgetplan_info->id,
                'fund_agency_id'=>$budgetplan_info->fund_agency_id,
                'year'=>$budgetplan_info->year,
                'district_id'=>$budgetplan_info->district_id,
                'block_id'=>$budgetplan_info->block_id,
                'phase'=>$phase,
                'category'=>$category,
                'agency_type_id' => $agency_type_id
             ];

            $data['budget_summery']=$budget_summery=$this->budgetModel->getBudgetSummeryByBlock($filter);
            
            
            /* total phy and fin */
            $totalPhy = 0;
            $totalFin = 0.00;

            foreach ($budget_summery as $item) {
                $totalPhy += $item['phy'];
                $totalFin += $item['fin'];
            }

            $data['budget_summery_total'] = [
                "total_phy" => $totalPhy,
                "total_fin" => $totalFin
            ];
            $data['block_budgets']=[];
            foreach($budget_summery as $summery){
                $agency_type_id = 0;
                if($summery['district_id']==0 && $summery['block_id']==0){
                    $agency_type_id = 8;
                } else if($summery['district_id']!=0 && $summery['block_id']==0){
                    $agency_type_id = 7;
                } else if($summery['district_id']!=0 && $summery['block_id']!=0){
                    $agency_type_id = 5;
                }
                $bfilter=[
                    'budget_plan_id'=>$summery['budget_plan_id'],
                    'fund_agency_id'=>$summery['fund_agency_id'],
                    'year'=>$summery['year_id'],
                    'district_id'=>$summery['district_id'],
                    'block_id'=>$summery['block_id'],
                    'agency_type_id' => $agency_type_id,
                ];
                $agency=$summery['block_id']==0?'ATMA':'Block';
                
                
                $data['block_budgets']['tabs'][]=[
                    'district_id'=>$summery['district_id'],
                    'block_id'=>$summery['block_id'],
                    'name'=>$summery['block_name']."(".$agency.")",
                ];
                
                $components=$this->budgetModel->getBudgetDetails($bfilter);
                $components = $this->buildTree($components);
                $data['block_budgets']['details'][] = $this->getBTable($components,'show');

            }
            $cum_tab=[
                'district_id'=>$budgetplan_info->district_id,
                'block_id'=>$budgetplan_info->block_id,
                'name'=>"All",
            ];
            array_unshift( $data['block_budgets']['tabs'],$cum_tab);

            $cum_component=$this->budgetModel->getCumulativeBudgetDetails($filter);
            $cum_component = $this->buildTree($cum_component);
            $cumulative_details = $this->getBTable($cum_component,'show');
            array_unshift($data['block_budgets']['details'],$cumulative_details);
            $data['approve']=$budgetplan_info->status;
            $data['approve_form']=$this->getApproveForm($budgetplan_info);
            return $this->template->view('Admin\Budgets\Views\budgetDetailsForm', $data);

        }else{
           
        }
    }

    private function getApproveForm($budgetplan_info){
        
        $data['title']='Budget Approve';
        $data['statuses'] = [     
            [
                'id' => 1,
                'name' => 'Approved',
            ],
            [
                'id' => 2,
                'name' => 'Rejected',
            ],
        ];

        $data['status_id'] = $budgetplan_info->status;
        $data['remarks'] = $budgetplan_info->remarks;
        return view('\Admin\Transaction\Views\approve_form',$data);

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
    
    protected function validateBulkBudget(){
        $rules=[
           
            /*'block_id' => array(
                'label' => 'Block',
                'rules' => 'required'
            ),*/

        ];

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

    public function view(){
        $data=[];
        $data['heading_title'] 	= "Budgets Details";

        if ($this->request->getMethod(1) === 'POST'){
            $budget_plan_id=$this->uri->getSegment(4);
            
            $this->budgetModel->editBudget($budget_plan_id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Budget Updated Successfully.');

            return redirect()->to(admin_url('budgets/view/'.$budget_plan_id));

        }

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $budgetplan_info = $this->budgetPlanModel->getBudgetPlan($this->uri->getSegment(4));
           // $agencyphase=(new BlockModel())->getTotalPhaseByAgency($budgetplan_info->fund_agency_id);
           
            $data['text_form'] = "Budget Details for ".$budgetplan_info->fund_agency."-".$budgetplan_info->yname."-".$budgetplan_info->district."-".$budgetplan_info->block;

        }
        
        if($budgetplan_info){
            if($budgetplan_info->block_id){
                $phase=1;
                $category=['program'];
            }else if($budgetplan_info->district_id){
                $phase=0;
                $category=['pmu', 'addl', 'procurement','iyom'];
            }else{
                $phase=0;
                $category=['pmu', 'addl', 'procurement','iyom'];
            }
                
            /*$pmubudgetplan=[
                'phase_no'=>0,
                'name'=>'PMU/Add/Procurement Component',
                'category'=>['pmu', 'addl', 'procurement']
            ];
            array_push($agencyphase,$pmubudgetplan);
            //printr($agencyphase);
            //exit;*/
            $agency_type_id = 0;
            if($budgetplan_info->district_id==0 && $budgetplan_info->block_id==0){
                $agency_type_id = 8;
            } else if($budgetplan_info->district_id!=0 && $budgetplan_info->block_id==0){
                $agency_type_id = 7;
            } else if($budgetplan_info->district_id!=0 && $budgetplan_info->block_id!=0){
                $agency_type_id = 5;
            }
            $filter=[
                'budget_plan_id'=>$budgetplan_info->id,
                'fund_agency_id'=>$budgetplan_info->fund_agency_id,
                'year'=>$budgetplan_info->year,
                'district_id'=>$budgetplan_info->district_id,
                'block_id'=>$budgetplan_info->block_id,
                'phase'=>$phase,
                'category'=>$category,
                'agency_type_id' => $agency_type_id
             ];

            $components = $this->budgetModel->getBudgetDetails($filter);
            //dd($components);
            //$components=[];
            if(getCurrentYearId() == $budgetplan_info->year){
                $data['view']=$view="edit";
            }else{
                $data['view']=$view="show";
            }
            if($components) {
                $components = $this->buildTree($components);
                $data['components'][0]['year'] = $budgetplan_info->year;
                $data['components'][0]['phase'] = 0;
                $data['components'][0]['fund_agency_id'] = $budgetplan_info->fund_agency_id;
                $data['components'][0]['district_id'] = $budgetplan_info->district_id;
                $data['components'][0]['block_id'] = $budgetplan_info->block_id;
                //$data['components'][$key]['budgets']=$components;
                $data['components'][0]['budgets'] = $this->getBTable($components,$view);
            }
            

            return $this->template->view('Admin\Budgets\Views\budgetForm', $data);

        }else{

        }
    }

    private function getTable($array,$key) {
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
                    
                    <td><input type="text" class="form-control" name="phase['.$key.'][budget]['.$item['component_id'].'][units]" value="'.$item['units'].'"></td></td>
                    <td><input type="text" class="form-control rate" name="phase['.$key.'][budget]['.$item['component_id'].'][unit_cost]" value="'.$item['unit_cost'].'"></td>
                    <td><input type="text" class="form-control physical" name="phase['.$key.'][budget]['.$item['component_id'].'][physical]" value="'.$item['physical'].'"></td>
                    <td><input type="text" class="form-control financial" name="phase['.$key.'][budget]['.$item['component_id'].'][financial]" value="'.$item['financial'].'"></td>
                    ';
            }
            if (!empty($item['children'])){
                $html .= $this->getTable($item['children'],$key);
            }
            $html .= '</tr>';
        }

        return $html;

    }

    public function delete($id) {
        $this->budgetPlanModel->delete($id);
        $this->budgetModel->where('budget_plan_id',$id)->delete();
        return redirect()->to(admin_url('budgets'))->with('message','Budget deleted successfully');
    }
}
