<?php
namespace Admin\Components\Controllers;
use Admin\Components\Models\ComponentAssignModel;
use Admin\Users\Models\UserGroupModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use Admin\Components\Models\ComponentsAgencyAssignModel;
use Admin\Components\Models\ComponentsAssignModel;
use Admin\Localisation\Models\BlockModel;

class AgencyAssign extends AdminController {

    use TreeTrait;
    private $error = array();
    private $componentsAgencyAssignModel;

    function __construct(){
       $this->componentsAgencyAssignModel= new ComponentsAgencyAssignModel();
    }

    public function index(){
        
        $data['fund_agencies'] = $fund_agencies= (new BlockModel())->getFundAgencies();
        
        $count=$this->uri->getTotalSegments();

        if($this->uri->getSegment(4)){
            $fund_agency_id = $this->uri->getSegment(4);
        }else if($count==3){
            $fund_agency_id =$fund_agencies?$fund_agencies[0]['fund_agency_id']:0;
        }else{
            $fund_agency_id = 0;
        }

        $data['fund_agency_id'] = $fund_agency_id;
        
        if ($this->request->getMethod(1) === 'POST'){
            $component_agency=$this->request->getPost('component');
            
            $this->componentsAgencyAssignModel->saveComponentAgency($component_agency,$fund_agency_id);

            $message="Component Agency Updated Successfully";

            $this->session->setFlashdata('message', $message);

            return redirect()->to(admin_url('components/agencyassign'));
        }
        
       
        $data['agency_types']=$agency_types=(new UserGroupModel())->getAgencyTypes();
        $components=(new ComponentsAssignModel())->getAssignComponent($fund_agency_id);
        
        //$data['componentAgencies']=(new ComponentAgencyAssignModel())->findAll();
        foreach($components as &$component){
            $agencies=(new ComponentsAgencyAssignModel())->where(['component_id'=>$component['component_id'],'fund_agency_id'=>$fund_agency_id])->asArray()->findAll();
            $component['agencies'] = array_column($agencies, 'agency_type_id');
            
        }

       
        $components= !empty($components)?$this->buildTree($components):[];
        
        $data['components'] = $this->getATable($components,$agency_types);
       
        return $this->template->view('Admin\Components\Views\componentsAgencyAssign', $data);

    }

}
