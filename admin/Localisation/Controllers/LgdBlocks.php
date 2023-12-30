<?php
namespace Admin\Localisation\Controllers;

use App\Controllers\AdminController;
use Admin\Localisation\Models\LgdBlocksModel;
use Admin\Localisation\Models\ClusterModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;

class LgdBlocks extends AdminController
{
    private $error = array();
    private $blockModel;

    public function __construct()
    {
        $this->blockModel = new LgdBlocksModel();
    }

    public function index()
    {
        $this->template->set_meta_title(lang('Block.heading_title'));
        return $this->getList();
    }

 
    protected function getList()
    {

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => lang('Block.heading_title'),
            'href' => admin_url('lgdblock')
        );

        $this->template->add_package(array('datatable', 'select2'), true);

     
        $data['datatable_url'] = admin_url('lgdblock/search');

        $data['heading_title'] = lang('Block.heading_title');

        $data['text_list'] = lang('Block.heading_title');
        $data['text_no_results'] = lang('Block.text_no_results');
        $data['text_confirm'] = lang('Block.text_confirm');

     

        if (isset($this->error['warning'])) {
            $data['error'] = $this->error['warning'];
        }

        if ($this->request->getPost('selected')) {
            $data['selected'] = (array)$this->request->getPost('selected');
        } else {
            $data['selected'] = array();
        }

        $districtModel = new DistrictModel();
        $data['districts'] = $districtModel->orderBy('name')->findAll();

// dd($data);
        return $this->template->view('Admin\Localisation\Views\lgdBlock', $data);
    }

    public function search()
    {
        $requestData = $_REQUEST;
        $totalData = $this->blockModel->getTotals();
       
        $totalFiltered = $totalData;

        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'filter_district' => $requestData['district'],
           
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );
        // printr($filter_data);exit;
        $totalFiltered = $this->blockModel->getTotals($filter_data);

        $filteredData = $this->blockModel->getAll($filter_data);

        $datatable = array();
        foreach ($filteredData as $result) {

            $datatable[] = array(
                '<input type="checkbox" name="selected[]" value="' . $result->id . '" />',
                $result->name,
                $result->district,
               
            );

        }
        //printr($datatable);
        $json_data = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        );

        return $this->response->setContentType('application/json')
            ->setJSON($json_data);

    }

   
    protected function validateForm()
    {
        //printr($_POST);
        $validation = \Config\Services::validation();
        $id = $this->uri->getSegment(4);
        $regex = "(\/?([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor

        $rules = $this->blockModel->validationRules;


        if ($this->validate($rules)) {
            return true;
        } else {
            //printr($validation->getErrors());
            $this->error['warning'] = "Warning: Please check the form carefully for errors!";
            return false;
        }
        return !$this->error;
    }

}

/* End of file hmvc.php */
/* Location: ./application/widgets/hmvc/controllers/hmvc.php */