<?php 
namespace Admin\Components\Controllers;
use Admin\Components\Models\ComponentsModel;
use Admin\Localisation\Models\BlockModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;

class Components extends AdminController {

    use TreeTrait;

    private $error = array();

    function __construct(){
        $this->componentsModel=new ComponentsModel();
    }
    public function index() {
        $this->template->set_meta_title('Components');
        return $this->getList();
    }

    protected function getList() {

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'Components',
            'href' => admin_url('components')
        );

        $this->template->add_package(array('datatable'),true);

        $data['add'] = admin_url('components/add');
        $data['delete'] = admin_url('components/delete');
        $data['datatable_url'] = admin_url('components/search');

        $data['heading_title'] = 'Components';

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        return $this->template->view('Admin\Components\Views\components', $data);
    }

    public function search() {
        $requestData= $_REQUEST;
        $totalData = $this->componentsModel->getTotal();
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'order'  		 => $requestData['order'][0]['dir'],
            'sort' 			 => $requestData['order'][0]['column'],
            'start' 			 => $requestData['start'],
            'limit' 			 => $requestData['length']
        );
        $totalFiltered = $this->componentsModel->getTotal($filter_data);

        $filteredData = $this->componentsModel->getAll($filter_data);

        $datatable=array();
        foreach($filteredData as $result) {

            $action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-primary ajaxaction" href="'.admin_url('components/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
            $action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('components/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
            $action .= '</div>';

            $datatable[]=array(
                '<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
                $result->description,
                $result->slug,
                $result->row_type,
                $result->category,
                $result->tags,
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
        $this->template->set_meta_title("Components");

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){

            $id=$this->componentsModel->insert($this->request->getPost());
            $this->session->setFlashdata('message', 'Component Saved Successfully.');

            return redirect()->to(admin_url('components'));


        }
        $this->getForm();
    }

    public function edit(){
        $this->template->set_meta_title("Components");

        if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){
            $id=$this->uri->getSegment(4);

            $this->componentsModel->update($id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Component Updated Successfully.');

            return redirect()->to(admin_url('components'));

        }
        $this->getForm();
    }

    public function delete() {

        $id = $this->request->getPost('id');
        $this->componentsModel->delete($id);
        return $this->response->setJSON(['status'=>true]);

    }

    public function phase($fund_agency_id){
        $blockModel = new BlockModel();
        $phases=$blockModel->getTotalPhaseByAgency($fund_agency_id);
        return $this->response->setJSON($phases);
    }

    public function autocomplete(){
        $json = array();
        $search_term = $this->request->getPost('query');
        $components=$this->componentsModel->getAll(['filter_search'=>$search_term]);

        foreach($components as $component){
            $json[] = array(
                'component_id' => $component->id,
                'component_name' => $component->description,
            );
        }
        return $this->response->setJSON($json);

    }

    protected function getForm(){

        $this->template->add_package(array('tags-inputs'),true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => "Components",
            'href' => admin_url('components')
        );

        $data['heading_title'] 	= 'Components';
        $data['text_form'] = $this->uri->getSegment(3) ? "Components Edit" : "Components Add";
        $data['cancel'] = admin_url('components');

        if(isset($this->error['warning'])){
            $data['error'] 	= $this->error['warning'];
        }

        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $component_info = $this->componentsModel->find($this->uri->getSegment(4));
        }
        foreach($this->componentsModel->getFieldNames('soe_components') as $field) {
            if($this->request->getPost($field)) {
                $data[$field] = $this->request->getPost($field);
            } else if(isset($component_info->{$field}) && $component_info->{$field}) {
                $data[$field] = html_entity_decode($component_info->{$field},ENT_QUOTES, 'UTF-8');
            } else {
                $data[$field] = '';
            }
        }

        $data['categories']=[
            'addl'=>'Additional Component',
            'pmu'=>'PMU Component',
            'procurement'=>'Procurement Component',
            'program'=>'Program Component'
        ];
        echo $this->template->view('Admin\Components\Views\componentForm',$data);
    }

    protected function validateForm() {

        $validation =  \Config\Services::validation();
        $id=$this->uri->getSegment(4);

        $rules = $this->componentsModel->validationRules;

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

    /**
     * Recursive function for save menu position
     */
    private function update_position($parent, $children) {
        $cModel = new ComponentModel();
        $i = 1;

        //rakesh add fund_agency_id
        $data['fund_agency_id'] = 1;
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }
        
        foreach ($children as $k => $v) {
            $menu_id = (int)$children[$k]['id'];
            $node_info_array = array();
            $node_info_array['parent'] =  $parent;
            $node_info_array['sort_order'] = $i;
            //rakesh add fund_agency_id
            $node_info_array['fund_agency_id'] = $data['fund_agency_id'];

            $cModel->update($menu_id,$node_info_array);
            if (isset($children[$k]['children'][0])) {
                $this->update_position($menu_id, $children[$k]['children']);
            }
            $i++;
        }
    }

}
