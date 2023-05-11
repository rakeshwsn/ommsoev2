<?php
namespace Admin\Components\Controllers;
use Admin\Components\Models\ComponentAssignModel;
use Admin\Users\Models\UserGroupModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use Admin\Components\Models\ComponentsAgencyAssignModel;
use Admin\Components\Models\ComponentsAssignModel;

class AgencyAssign extends AdminController {

    use TreeTrait;
    private $error = array();
    private $componentsAgencyAssignModel;

    function __construct(){
       $this->componentsAgencyAssignModel= new ComponentsAgencyAssignModel();
    }

    public function index(){
        
        
        if ($this->request->getMethod(1) === 'POST'){
            $component_agency=$this->request->getPost('component');
            
            $this->componentsAgencyAssignModel->saveComponentAgency($component_agency);

            $message="Component Agency Updated Successfully";

            $this->session->setFlashdata('message', $message);

            return redirect()->to(admin_url('components/agencyassign'));
        }
        
        $data['agency_types']=(new UserGroupModel())->getAgencyTypes();
        $components=(new ComponentsAssignModel())->getAssignComponent(1);
        
        //$data['componentAgencies']=(new ComponentAgencyAssignModel())->findAll();
        foreach($components as &$component){
            $agencies=(new ComponentsAgencyAssignModel())->where(['component_id'=>$component['component_id']])->asArray()->findAll();
            $component['agencies'] = array_column($agencies, 'agency_type_id');
            //foreach()
        }

       
        $data['components']= !empty($components)?$this->buildTree($components):[];
       //printr($data['components']);
        return $this->template->view('Admin\Components\Views\componentsAgencyAssign', $data);

    }

}
