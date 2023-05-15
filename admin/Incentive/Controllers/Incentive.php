<?php
namespace Admin\Incentive\Controllers;

use Admin\Incentive\Models\IncentivemainModel;
use Admin\Incentive\Models\IncentiveModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class Incentive extends AdminController{
	private $error = array();
	//private $incentiveModel;

	public function __construct(){
		$this->incentiveModel=new IncentiveModel();
		$this->incentivemainModel=new IncentivemainModel();
    }
	
	public function index(){
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		return $this->getListMain();  
	}

	protected function getListMain() {
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$this->template->add_package(array('datatable','select2'),true);
		$data['addform'] = admin_url('incentive/addform');
		$data['searchview'] = admin_url('incentive/incentivesearch');

		$data['heading_title'] = lang('Incentive.heading_title');
		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');
		
		$data['button_add'] = lang('Incentive.button_add');
		$data['button_view'] = lang('Incentive.button_view');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');

		$data['session_id'] =  $this->session->get('user')->id ;

		$data['datatable_url'] = admin_url('incentive/searchmain');
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}

		$this->filterOptionsMain($data);

		return $this->template->view('Admin\Incentive\Views\incentivemain', $data);
	}

	public function searchMain() {
		$requestData= $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		$filter_data = array(
				'filter_district'	=> $requestData['searchBydistrictId'],
				'filter_block'	    => $requestData['searchByblockId'],
		        'filter_year'		=> $requestData['searchByYear'],
				'filter_season'     =>	$requestData['searchBySeason'],
				'filter_search'     => 	$requestData['search']['value'],
				'order'  		    => $requestData['order'][0]['dir'],
				'sort' 			    => $requestData['order'][0]['column'],
				'start' 		    => 	$requestData['start'],
				'limit' 		    =>	$requestData['length']
			);
		
		//$totalFiltered = $this->incentiveModel->getTotal($filter_data);
		$filteredData = $this->incentivemainModel->getAll($filter_data);
	
		//printr($filteredData);
		$datatable=array();
		foreach($filteredData as $result) {

			if($result->year == 1){
				$year = '2017-18';
			} else if($result->year == 2){
				$year = '2018-19';
			} else if($result->year == 3){
				$year = '2020-21';
			} else if($result->year == 4){
				$year = '2021-22';
			}


			if($result->season == 1){
				$season = 'kharif';
			} else if($result->season == 2){
				$season = 'Rabi';
			}
			
			$action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('incentive/view/'.$result->id).'">District/Block View Data</a>';
			$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('incentivemain/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';

			if($result->pdf){
			$pdfupload ='<a href="'.base_url() .'/uploads/farmerincentive/'.$result->pdf.'">';
			$pdfupload .= '<i class="fa fa-file-pdf-o" style="font-size:48px;color:red"></i>';
			$pdfupload .= '</a>';
			} else {
				$pdfupload ='<h5>Not Uploaded </h5>';
			}
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->district_name,
				$result->block_name,
                $year,
                $season,
				$pdfupload,
				$action
			);
			
	
		} 
		$json_data = array(
			"draw"            => isset($requestData['draw']) ? intval( $requestData['draw'] ):1,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $datatable
		);
		
		return $this->response->setContentType('application/json')
								->setJSON($json_data);
		
	}


	public function incentivesearch(){
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$this->template->add_package(array('datatable','select2'),true);
		$data['addform'] = admin_url('incentive/addform');
		$data['searchview'] = admin_url('incentive/incentivesearch');

		$data['heading_title'] = lang('Incentive.heading_title');
		
		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');
		
		$data['button_add'] = lang('Incentive.button_add');
		$data['button_view'] = lang('Incentive.button_view');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');
		$data['datatable_url'] = admin_url('incentive/searchall');
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		$this->filterOptionsMain($data);

		return $this->template->view('Admin\Incentive\Views\incentiveallview', $data); 
	}


	public function searchall() {
		$requestData= $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		$filter_data = array(
				'filter_district'=>$requestData['searchBydistrictId'],
				'filter_block'=>$requestData['searchByblockId'],
				'filter_year'=>$requestData['searchByYear'],
				'filter_season'=>$requestData['searchBySeason'],
				'filter_search' => $requestData['search']['value'],
				'order'  		 => $requestData['order'][0]['dir'],
				'sort' 			 => $requestData['order'][0]['column'],
				'start' 			 => $requestData['start'],
				'limit' 			 => $requestData['length']
			);
		
		//$totalFiltered = $this->incentiveModel->getTotal($filter_data);
		$filteredData = $this->incentivemainModel->getAllsearch($filter_data);
	
		//printr($filteredData);
		$datatable=array();
		foreach($filteredData as $result) {

			if($result->year == 1){
				$year = '2017-18';
			} else if($result->year == 2){
				$year = '2018-19';
			} else if($result->year == 3){
				$year = '2020-21';
			} else if($result->year == 4){
				$year = '2021-22';
			}

			if($result->season == 1){
				$season = 'kharif';
			} else if($result->season == 2){
				$season = 'Rabi';
			}
			
			// $action  = '<div class="btn-group btn-group-sm pull-right">';
            // $action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('incentive/view/'.$result->id).'"><i class="fa fa-eye"></i></a>';
			// $action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('incentivemain/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			// $action .= '</div>';
			
			$datatable[]=array(
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->district_name,
				$result->block_name,
                $year,
                $season,
				$result->gp,
				$result->village,
				$result->name,
				$result->spouse_name,
				$result->gender,
				$result->caste,
				$result->phone_no,
				$result->aadhar_no,
				$result->year_support,
				$result->area_hectare,
				$result->bank_name,
				$result->account_no,
				$result->ifsc,
				$result->amount,
				
			);
			
	
		} 
		$json_data = array(
			"draw"            => isset($requestData['draw']) ? intval( $requestData['draw'] ):1,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $datatable
		);
		
		return $this->response->setContentType('application/json')
								->setJSON($json_data);
		
	}





	protected function filterOptionsMain(&$data){

        $data['years'] = getAllYears();
        $data['months'] = getAllMonths();
		$districtModel=new DistrictModel();
        $data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
        if(isset($data['district_id']) && $data['district_id']){
            $blockModel = new BlockModel();
            $data['blocks'] = $blockModel->where(['district_id'=>$data['district_id']])->findAll();
        }
		//print_r($data['blocks']); exit;

        $data['seasons'] = [
           1 => 'kharif',2 =>'rabi'
        ];
		// echo "<pre>";
		// print_r($data); exit;
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        } else {
            $data['year_id'] = getCurrentYearId();
        }
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        } else {
            $data['month_id'] = getCurrentMonthId();
        }
        if($this->request->getGet('season')){
            $data['season'] = $this->request->getGet('season');
        } else {
            $data['season'] = getCurrentSeason();
        }

        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = 0;
        }
		$this->template->add_package(array('datatable','select2'),true);
        $data['filter_panel'] = view('Admin\Incentive\Views\filter', $data);
    }


	


	public function addform(){
		$this->template->add_package(array('select2'),true);
		$user_upload =  $this->session->get('user')->id ;
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		$data['text_form'] = $this->uri->getSegment(4) ? "INCENTIVE EDIT" : "INCENTIVE ADD";
		$data['cancel'] = admin_url('incentive');
		$data['years'] = getAllYears();
        $data['months'] = getAllMonths();
		$districtModel=new DistrictModel();
		$data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
        if(isset($data['district_id']) && $data['district_id']){
            $blockModel = new BlockModel();
            $data['blocks'] = $blockModel->where(['district_id'=>$data['district_id']])->findAll();
        }
		
		$data['seasons'] = [
            'kharif','rabi'
        ];
		$data['msgclass'] = '';
		if ($this->request->getMethod(1) === 'POST'){
			
			//printr($file_pdf); exit;
			$check['district_id'] = $_POST['district_id'];
			$check['block_id'] = $_POST['block_id'];
			$check['year'] = $_POST['year'];
			$check['season'] = $_POST['season'];

			$checkedData = $this->incentiveModel->getcheckExsists($check);

			if($checkedData > 0){
				$this->session->setFlashdata('errorupload', 'Data Already Exists');
				$data['msgclass'] = 'bg-danger';
			
			} else{

				if(isset($_FILES["file"]["name"])){
					$file_pdf = $this->request->getFile('pdf');
					$file_pdf->move(DIR_UPLOAD . 'farmerincentive', $file_pdf->getName());
					$main_incetive_data = array(
						'district_id' =>$check['district_id'],
						'block_id' =>$check['block_id'],
						'year' =>$check['year'],
						'season' =>$check['season'],
						'pdf' => $file_pdf->getName(),
						'created_by' => $user_upload
					);
					$result_main= $this->incentiveModel->addInceitive_main($main_incetive_data);
					//print_r($_FILES); exit;
					$fileName = $_FILES["file"]["tmp_name"];
					$reader = IOFactory::createReader('Xlsx');
					$spreadsheet = $reader->load($fileName);
					$activesheet = $spreadsheet->getSheet(0);

					$row_data = $activesheet->toArray();
				
					// echo "<pre>";
					// print_r($main); exit;
					if ($_FILES["file"]["size"] > 0) {
				
					 foreach ($row_data  as $key=>$column) {
					   if($key>=1){
				 
						 $datacsv[]= array(
						   'incetive_id' =>$result_main,
						   'gp' =>$column[0],
						   'village' => $column[1], 
						   'name' => $column[2], 
						   'spouse_name' => $column[3], 
						   'gender' => $column[4], 
						   'caste' => $column[5], 
						   'phone_no' => $column[6], 
						   'aadhar_no' => $column[7], 
						   'year_support' => $column[8], 
						   'area_hectare' => $column[9], 
						   'bank_name' => $column[10],
						   'account_no' => $column[11], 
						   'ifsc' => $column[12],
						   'amount' => $column[13],
						   'uploaded_by' => $user_upload
				 		 );
					   }
					   
					 }
					
					$res= $this->incentiveModel->addInceitive($datacsv);
					$this->session->setFlashdata('errorupload', 'Data Uploaded successfully');
					$data['msgclass'] = 'bg-success';
					return redirect()->to(base_url('admin/incentive'));
				   }
				 }
			}


		}
		
		return $this->template->view('Admin\Incentive\Views\form', $data);  
	}

	public function view(){
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		return $this->getList();  
	}
	
	public function add(){
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		if ($this->request->getMethod(1) === 'POST'){

			
			print_r($_POST); exit;
		}
		$this->getForm();
	}
	
	public function edit(){
		$this->template->set_meta_title(lang('Incentive.heading_title'));
		if ($this->request->getMethod(1) === 'POST'){	
			$id=$this->uri->getSegment(4);
			// echo "<pre>";
            // print_r($_POST); exit;
			$this->incentiveModel->update($id,$this->request->getPost());
            $this->session->setFlashdata('message', 'Incentive Updated Successfully.');
		
			return redirect()->to(base_url('admin/incentive'));
		}
		$this->getForm();
	}
	
	public function delete(){
		//$deleteid=$this->uri->getSegment(4); 
		if ($this->request->getPost('selected')) {
            $selected = $this->request->getPost('selected');
        } else {
            $selected = (array) $this->uri->getSegment(4);
        }
        $this->incentiveModel->deleteincentive($selected);

        //$this->slugModel->whereIn('route_id', $selected)->delete();

		$this->session->setFlashdata('message', 'Incentive deleted Successfully.');
		return redirect()->to(base_url('admin/incentive'));
	}
	
	protected function getList() {

	    $data['mainincetiveid']=$this->uri->getSegment(4); 
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		$this->template->add_package(array('datatable','select2'),true);
		$data['addform'] = admin_url('incentive/addform');
		$data['delete'] = admin_url('incentive/delete');

		$data['heading_title'] = lang('Incentive.heading_title');
		
		$data['text_list'] = lang('Incentive.text_list');
		$data['text_no_results'] = lang('Incentive.text_no_results');
		$data['text_confirm'] = lang('Incentive.text_confirm');
		
		$data['button_add'] = lang('Incentive.button_add');
		$data['button_edit'] = lang('Incentive.button_edit');
		$data['button_delete'] = lang('Incentive.button_delete');
		$data['datatable_url'] = admin_url('incentive/search');
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		$this->filterOptions($data);

		return $this->template->view('Admin\Incentive\Views\index', $data);
	}
	
	public function search() {
		$requestData= $_REQUEST;
		$totalData = $this->incentiveModel->getTotal();
		$totalFiltered = $totalData;
		$filter_data = array(
				'mainincetiveid'=>$requestData['mainincetiveid'],
				'filter_search' => $requestData['search']['value'],
				'order'  		 => $requestData['order'][0]['dir'],
				'sort' 			 => $requestData['order'][0]['column'],
				'start' 			 => $requestData['start'],
				'limit' 			 => $requestData['length']
			);
		$filteredData = $this->incentiveModel->getAll($filter_data);
	
		
		$datatable=array();
		foreach($filteredData as $result) {
			$error = 'false';
			//validation rules
			if(!preg_match('/^[0-9]{10}+$/', $result->phone_no) || empty($result->phone_no)){
                $error  = 'true';
			} else if(!preg_match('/^[0-9]{12}+$/', $result->aadhar_no) || empty($result->aadhar_no)){
				$error  = 'true';
			}else if($result->area_hectare == 0 || $result->area_hectare > 9.99 || empty($result->area_hectare)){
				$area_hectare  = 'true';
			}else if(!preg_match('/^[0-9]{9,18}$/', $result->account_no) || empty($result->account_no)){
				$error  = 'true';
			}else if(!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $result->ifsc) || empty($result->ifsc)){
				 $error  = 'true';
			}
			
			
			
			$action  = '<div class="btn-group btn-group-sm pull-right">';
            $action .= 		'<a class="btn btn-sm btn-primary" href="'.admin_url('incentive/edit/'.$result->id).'"><i class="fa fa-pencil"></i></a>';
		//	$action .=		'<a class="btn-sm btn btn-danger btn-remove" href="'.admin_url('incentive/delete/'.$result->id).'" onclick="return confirm(\'Are you sure?\') ? true : false;"><i class="fa fa-trash-o"></i></a>';
			$action .= '</div>';
			
			$datatable[]=array(
				$error,
				'<input type="checkbox" name="selected[]" value="'.$result->id.'" />',
				$result->name,
				$result->spouse_name,
                $result->gender,
                $result->caste,
				$action
			);
			
	
		} 
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
			'text' => lang('Incentive.heading_title'),
			'href' => admin_url('incentive')
		);
		

		$data['heading_title'] 	= lang('Incentive.heading_title');
		$data['text_form'] = $this->uri->getSegment(4) ? "Incentive Edit" : "Incentive Add";
		$data['cancel'] = admin_url('incentive');
		
		if(isset($this->error['warning'])){
			$data['error'] 	= $this->error['warning'];
		}
		
		if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
			$incentive_info = $this->incentiveModel->find($this->uri->getSegment(4));
		}
		
		foreach($this->incentiveModel->getFieldNames('detailed_incentive_data') as $field) {
			if($this->request->getPost($field)) {
				$data[$field] = $this->request->getPost($field);
			} else if(isset($incentive_info->{$field}) && $incentive_info->{$field}) {
				$data[$field] = html_entity_decode($incentive_info->{$field},ENT_QUOTES, 'UTF-8');
			} else {
				$data[$field] = '';
			}
		}

		$districtModel=new DistrictModel();
        $data['districts'] = $districtModel->getAll();

        $data['blocks'] = [];
        if(isset($data['district_id']) && $data['district_id']){
            $blockModel = new BlockModel();
            $data['blocks'] = $blockModel->where(['district_id'=>$data['district_id']])->findAll();
        }
		echo $this->template->view('Admin\Incentive\Views\incentiveform',$data);
	}
	
	protected function validateForm() {
		//printr($_POST);
		$validation =  \Config\Services::validation();
		$id=$this->uri->getSegment(4);
		$regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

		$rules = $this->incentiveModel->validationRules;

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

	protected function filterOptions(&$data){

        $data['years'] = getAllYears();
        $data['months'] = getAllMonths();
		$districtModel=new DistrictModel();
        $data['districts'] = $districtModel->getAll();

		$data['blocks'] = [];
        if(isset($data['district_id']) && $data['district_id']){
            $blockModel = new BlockModel();
            $data['blocks'] = $blockModel->where(['district_id'=>$data['district_id']])->findAll();
        }
		//print_r($data['blocks']); exit;

        $data['seasons'] = [
           1 => 'kharif',2 =>'rabi'
        ];
		// echo "<pre>";
		// print_r($data); exit;
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        } else {
            $data['year_id'] = getCurrentYearId();
        }
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        } else {
            $data['month_id'] = getCurrentMonthId();
        }
        if($this->request->getGet('season')){
            $data['season'] = $this->request->getGet('season');
        } else {
            $data['season'] = getCurrentSeason();
        }

        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
        } else {
            $data['district_id'] = 0;
        }
		$this->template->add_package(array('datatable','select2'),true);
        $data['filter_panel'] = view('Admin\Incentive\Views\filter', $data);
    }

}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */