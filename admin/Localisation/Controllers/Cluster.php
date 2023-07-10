<?php
namespace Admin\Localisation\Controllers;
use App\Controllers\AdminController;
use Admin\Localisation\Models\ClusterGPModel;
use Admin\Localisation\Models\ClusterModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;

class Cluster extends AdminController{
	private $error = array();
	private $clusterModel;
	private $clustergpModel;
	
	public function __construct(){
		$this->clusterModel=new ClusterModel();
        $this->clustergpModel=new ClusterGPModel();
	}
	
	public function index(){
		$this->template->set_meta_title(lang('Cluster.heading_title'));
		return $this->getList();  
	}
	
	public function add(){
		
		$this->template->set_meta_title(lang('Cluster.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			
			//$code=$this->clusterModel->getClusterCode($this->request->getPost());
           // printr($this->request->getPost());

            $id=$this->clusterModel->insert($this->request->getPost());
            //cluster_gp
            if($this->request->getPost('gp_id')) {


                foreach ($this->request->getPost('gp_id') as $gp) {
                    $cluster_gp = [
                        'cluster_id' => $id,
                        'district_id' => $this->request->getPost('district_id'),
                        'block_id' => $this->request->getPost('block_id'),
                        'gp_id' => $gp
                    ];
                    $this->clustergpModel->insert($cluster_gp);
                }
            }
			
			$this->session->setFlashdata('message', 'Cluster Saved Successfully.');
			
			return redirect()->to(base_url('admin/cluster'));
		}
		$this->getForm();
	}
	
	public function edit(){
		
		$this->template->set_meta_title(lang('Cluster.heading_title'));
		
		if ($this->request->getMethod(1) === 'POST' && $this->validateForm()){	
			$id=$this->uri->getSegment(4);
			$this->clusterModel->update($id,$this->request->getPost());
            $cluster = $this->clusterModel->find($id);

            $this->clustergpModel->where('cluster_id', $id)->delete();
            if($this->request->getPost('gp_id')) {
                foreach ($this->request->getPost('gp_id') as $gp) {
                    $cluster_gp = [
                        'cluster_id' => $id,
                        'district_id' => $this->request->getPost('district_id'),
                        'block_id' => $this->request->getPost('block_id'),
                        'gp_id' => $gp
                    ];
                    $this->clustergpModel->insert($cluster_gp);
                }
            }

            $this->session->setFlashdata('message', 'Cluster Updated Successfully.');
		
			return redirect()->to(base_url('admin/cluster'));
		}
		$this->getForm();
	}
	
	public function delete(){
		if ($this->request->getPost('selected')){
			$selected = $this->request->getPost('selected');
		}else{
			$selected = (array) $this->uri->getSegment(4);
		}
		$this->clusterModel->delete($selected);
		
		$this->session->setFlashdata('message', 'Cluster deleted Successfully.');
		return redirect()->to(base_url('admin/cluster'));
	}
	
	protected function getList() {
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Cluster.heading_title'),
			'href' => admin_url('cluster')
		);
		
		$this->template->add_package(array('datatable','datatable_export','select2'),true);

		$data['add'] = admin_url('cluster/add');
		$data['delete'] = admin_url('cluster/delete');
		$data['datatable_url'] = admin_url('cluster/search');

		$data['heading_title'] = lang('Cluster.heading_title');
		
		$data['text_list'] = lang('Cluster.text_list');
		$data['text_no_results'] = lang('Cluster.text_no_results');
		$data['text_confirm'] = lang('Cluster.text_confirm');
		
		$data['button_add'] = lang('Cluster.button_add');
		$data['button_edit'] = lang('Cluster.button_edit');
		$data['button_delete'] = lang('Cluster.button_delete');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		if ($this->request->getPost('selected')) {
			$data['selected'] = (array)$this->request->getPost('selected');
		} else {
			$data['selected'] = array();
		}
		
		$districtModel=new DistrictModel();
		$data['districts'] = $districtModel->getAll();
		
	

		return $this->template->view('Admin\Localisation\Views\cluster', $data);
	}
	
	public function search() {
		$requestData= $_REQUEST;
		$totalData = $this->clusterModel->getTotals();
		$totalFiltered = $totalData;
		
		$filter_data = array(
			'filter_search' => $requestData['search']['value'],
			'filter_district' => $requestData['district'],
			'filter_cluster' => $requestData['cluster'],
			'order'  		 => $requestData['order'][0]['dir'],
			'sort' 			 => $requestData['order'][0]['column'],
			'start' 			 => $requestData['start'],
			'limit' 			 => $requestData['length']
		);
		$totalFiltered = $this->clusterModel->getTotals($filter_data);
			
		$filteredData = $this->clusterModel->getAll($filter_data);
		
		$datatable=array();
		foreach($filteredData as $result) {

			$action  = '<div class="btn-group btn-group-sm pull-right">';
			$action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('cluster/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('cluster/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->code,
				$result->name,
				$result->district,
                $result->block,
                $result->gp,
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
	
	protected function getForm(){
		
		$this->template->add_package(array('select2'),true);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Cluster.heading_title'),
			'href' => admin_url('cluster')
		);
		
		//printr($_SESSION);
		$_SESSION['isLoggedIn'] = true;
        
		$data['heading_title'] 	= lang('Cluster.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Cluster Edit" : "Cluster Add";
		$data['cancel'] = admin_url('cluster');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$cluster_info = $this->clusterModel->find($this->uri->getSegment(4));
		}
		
		foreach($this->clusterModel->getFieldNames('cluster') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($cluster_info->{$field}) && $cluster_info->{$field}) {
				$data[$field] = html_entity_decode($cluster_info->{$field},ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}
        $grampanchayats = array();
        if ($this->request->getPost('gp_id')) {
            $grampanchayats = $this->request->getPost('gp_id');
        } elseif ($this->uri->getSegment(4)) {
            $gps = $this->clustergpModel->where('cluster_id', $this->uri->getSegment(4))->findAll();
            foreach($gps as $b) {
                $grampanchayats[]=  $b['gp_id'];
            }
		}
        $data['grampanchayats'] = $grampanchayats;


		$districtModel=new DistrictModel();
		$data['districts'] = $districtModel->getAll();
		
	
		echo $this->template->view('Admin\Localisation\Views\clusterForm',$data);
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->clusterModel->validationRules;

		
		if ($this->validate($rules)){
			return true;
    	}
		else{

			$this->error['warning']="Warning: Please check the form carefully for errors!";
			return false;
    	}
		return !$this->error;
	}
	
	public function grampanchayat($cluster=''){
		if (is_ajax()){
			$grampanchayatModel=new GrampanchayatModel();
			$json = array(
				'cluster'  	=> $cluster,
				'grampanchayat'   => $grampanchayatModel->getGpsByCluster($cluster)
			);
			echo json_encode($json);
		}else{
         	return show_404();
      	}
	}
	
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */