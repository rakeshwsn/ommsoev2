<?php
namespace Admin\Letters\Controllers;
use Admin\Letters\Models\LetterModel;
use App\Controllers\AdminController;
use Admin\Letters\Models\LetteruserModel;
use Config\Url;

class Letter extends AdminController{
    private $error = array();
    private $letterModel;

	public function __construct(){
		$this->letterModel=new LetterModel();
    }

    public function index(){
        $this->template->add_package(['datatable','uploader','jquery_loading'],true);
		$this->template->set_meta_title('Letters');
		$data = [];

		$data['heading_title'] = 'Letters';

		$data['letter_no'] = $this->letterModel->getNewLetterNo();
		$data['subject'] = '';
		$data['user_id'] = '';
		$data['users'] = (new LetteruserModel())->findAll();

        $data['datatable_url'] = Url::lettersDatatable;
        $data['add_url'] = Url::lettersAdd;
        $data['delete_url'] = Url::lettersDelete;
        return $this->template->view('Admin\Letters\Views\letter', $data);
	}

    public function search() {
        $requestData= $_REQUEST;
        $totalData = $this->letterModel->getTotal();

        if(isset($requestData['custom_fields'][0]['name']) && $requestData['custom_fields'][0]['name']) {
            $filter_search = $requestData['custom_fields'];
            parse_str($filter_search[0]['value'], $filter_search);
            $filter_search['name'] = $requestData['custom_fields'][0]['name'];
        } else {
            $filter_search = $requestData['search']['value'];
        }

        $order_columns = array(
            'l.id','created_at','letter_no','user_name','subject'
        );
        $filter_data = array(
            'filter_search' => $filter_search,
            'order' => $requestData['order'][0]['dir'],
            'sort' => $order_columns[$requestData['order'][0]['column']],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );

        $totalFiltered = $this->letterModel->getTotal($filter_data);

        $filteredData = $this->letterModel->getAll($filter_data);

        $datatable=array();
        foreach($filteredData as $result) {

            $action = '<div class="pull-right">';
            $action .= '<a class="btn btn-sm btn-danger btn-delete" data-letter="'.$result->letter_no.'" data-id="'.$result->id.'" href="#"><i class="fa fa-trash"></i></a>';
            $action .= '</div>';

            $datatable[] = [
                $result->id,
                ymdToDmy($result->created_at),
                $result->letter_no,
                $result->user_name,
                $result->subject,
                $action
            ];

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

    public function add() {
        $data['offset'] = 0;

        $post = [
            'letter_no' => $this->request->getPost('letter_no'),
            'subject' => $this->request->getPost('subject'),
            'user_id' => $this->request->getPost('user'),
            'team' => 'state',
        ];

        $letter_exists = $this->letterModel->isLetterExists($this->request->getPost('letter_no'),getCurrentYearId());
        if($letter_exists){
            $post['letter_no'] = $this->letterModel->getNewLetterNo();
        }

        $id = $this->letterModel->insert($post);

        $data['next_letter_no'] = $this->letterModel->getNewLetterNo();
        if($id){
            $data['success'] = true;
        } else {
            $data['success'] = false;
        }

        return $this->response->setContentType('application/json')
            ->setJSON($data);
	}

    public function delete(){

        $id = $this->request->getPost('id');
        if($id){
            $this->letterModel->delete($id);
        }

        $data['next_letter_no'] = $this->letterModel->getNewLetterNo();
        if($id){
            $data['success'] = true;
        } else {
            $data['success'] = false;
        }

        return $this->response->setContentType('application/json')
            ->setJSON($data);
    }
}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */