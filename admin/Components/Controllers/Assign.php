<?php
namespace Admin\Components\Controllers;
use Admin\Components\Models\ComponentsAssignModel;
use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;

class Assign extends AdminController {

    use TreeTrait;

    private $error = array();

    function __construct(){
       $this->componentsAssignModel=new ComponentsAssignModel();
    }

    public function index(){
        
        $this->template->add_package(array('jquerynestable','sweetalert','typehead'),true);

        if ($this->request->getMethod(1) === 'POST'){
            $fund_agency_id=$this->request->getPost('fund_agency_id');

            $component = json_decode($this->request->getPost('component_data'), true, 64);
            $this->update_position(0, $component);

            $message="Component Updated Successfully";

            $this->session->setFlashdata('message', $message);

            return redirect()->to(admin_url('components/assign/'.$fund_agency_id));
        }


        $data['fund_agencies'] = $fund_agencies= (new BlockModel())->getFundAgencies();
        $data['component_url']=admin_url('components/autocomplete');
        $count=$this->uri->getTotalSegments();

        if($this->uri->getSegment(4)){
            $fund_agency_id = $this->uri->getSegment(4);
        }else if($count==3){
            $fund_agency_id =$fund_agencies?$fund_agencies[0]['fund_agency_id']:0;
        }else{
            $fund_agency_id = 0;
        }

        $data['fund_agency_id'] = $fund_agency_id;
        $data['add_url'] = admin_url('components/assign/add');
        $data['delete_url'] = admin_url('components/assign/delete');

        $data['components'] = $this->getHTMLTree($fund_agency_id);
        return $this->template->view('Admin\Components\Views\componentAssign', $data);

    }

    public function getHTMLTree($fund_agency_id) {
        $comps = $this->componentsAssignModel->getAssignComponent($fund_agency_id);

        $tree = !empty($comps)?$this->buildTree($comps):[];
        return $this->nestedHTMLTree($tree);
    }

    public function add(){
        $json = [];

        if ($this->request->getMethod(1) === 'POST'){
            $sort=$this->componentsAssignModel->getMaxSortorder($_POST['fund_agency_id']);
            $component_name=$this->request->getPost('component_name');
            $component_number=$this->request->getPost('number');
            $componentitem=array(
                "fund_agency_id"=>$this->request->getPost('fund_agency_id'),
                "component_id"=>$this->request->getPost('component_id'),
                "number"=>$this->request->getPost('number'),
                "parent"=>0,
                "sort_order"=>$sort+1
            );
            $menu_id=$this->componentsAssignModel->insert($componentitem);

            $li = '<li class="dd-item" data-id="' . $menu_id . '">'
                . '<div class="dd-handle">';

            $li .= '['.$component_number.'] '
                . $component_name
                . '&nbsp;&nbsp;&nbsp; <a href="#" class="btn-remove">Remove</a>'.'</div>';

            $li .= '</li>';
            $json = [
                "li" => $li,
                "success" => "Component added Successfully",
            ];
        }
        return $this->response->setJSON($json);
    }

    public function delete() {

        $id = $this->request->getPost('id');
        $this->componentsAssignModel->delete($id);
        return $this->response->setJSON(['status'=>true]);

    }

    /**
     * Recursive function for save menu position
     */
    private function update_position($parent, $children) {

        $i = 1;

        foreach ($children as $k => $v) {
            $menu_id = (int)$children[$k]['id'];
            $node_info_array = array();
            $node_info_array['parent'] =  $parent;
            $node_info_array['sort_order'] = $i;

            $this->componentsAssignModel->update($menu_id,$node_info_array);
            if (isset($children[$k]['children'][0])) {
                $this->update_position($menu_id, $children[$k]['children']);
            }
            $i++;
        }
    }

}
