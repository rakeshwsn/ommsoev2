<?php 
namespace Admin\Transaction\Controllers;
use Admin\Common\Models\AllowuploadModel;
use Admin\Common\Models\CommonModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\MIS\Models\MISModel;
use Admin\Transaction\Models\TransactionComponentModel;
use Admin\Transaction\Models\TransactionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use App\Traits\TreeTrait;
use Config\ExcelStyles;
use Config\Url;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class Transaction extends AdminController {

    use TreeTrait;
    private $cells = [
        'month' => 'A1',
        'year' => 'B1',
        'transaction_type' => 'C1',
        'fund_agency_id' => 'D1',
        'agency_type_id' => 'E1',
        'heading' => 'B2',
        'level' => 'B3',
        'block' => 'C3',
        'monthyear' => 'G3',
        'agency' => 'K3',
        'prev_cell' => 'G4',
        'during_cell' => 'I4',
        'cum_cell' => 'K4',
    ];

    public function index() {

        $this->template->add_package(['datatable','uploader','jquery_loading'],true);
        $data = [];
        $data['months'] = getMonths();
        $data['years'] = getAllYears();
        $data['download_url'] = Url::transactionDownloadTemplate;

        //control validation from env file
        $data['upload_enabled'] = false;
        if(env('soe.uploadDateValidation')){

            $upload_model = new AllowuploadModel();

            $ufilter = [
                'user_id' => $this->user->user_id
            ];

            $upload = $upload_model->getByDate($ufilter);

            $months = [];
            foreach ($upload as $item) {
                $months[] = $item['month'];
            }

            $data['upload_enabled'] = in_array(getCurrentMonthId(),$months);
        }
        
        $data['download_button'] = ($this->user->agency_type_id==$this->settings->block_user) && $data['upload_enabled'];
        $data['month_id'] = getCurrentMonthId();

        $data['year_id'] = getCurrentYearId();

        
        $data['fund_agencies'] = !$this->user->fund_agency_id ? (new BlockModel())->getFundAgencies():[];
        

        $data['mis_uploaded'] = false;
        $misModel = new MISModel();
        $mis_exist = $misModel->where([
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'agency_type_id' => $this->user->agency_type_id,
            'month' => $data['month_id'],
            'year' => $data['year_id'],
            'user_id' => $this->user->user_id,
        ])->first();

        if($mis_exist){
            $data['mis_uploaded'] = true;
        }
       
        $data['datatable_url'] = Url::transactionDatatable;
        $data['check_mis_url'] = Url::misIsUploaded;
        $data['upload_url'] = Url::transactionUpload;
        $data['add_url'] = Url::transactionAdd;
        $data['message'] = $this->session->getFlashdata('message');

        $this->filterOptions($data);

        return $this->template->view('Admin\Transaction\Views\index', $data);
    }

    private function filterOptions(&$data) {

        $data['blocks'] = [];
        /*if ($this->user->agency_type_id != $this->settings->block_user) {
            $block_model = new BlockModel();
            $filter = [
                'district_id' => $this->user->district_id,
                'fund_agency_id' => $this->user->fund_agency_id
            ];

            $data['blocks'] = $block_model->where($filter)->asArray()->findAll();
        }*/

        $data['agency_types'] = [];
        foreach ($this->settings->user_can_access as $user_group => $user_can_access_grp) {
            if ($this->user->agency_type_id == $user_group) {
                $data['agency_types'] = (new UserGroupModel)->whereIn('id',
                    $user_can_access_grp)->orderBy('name')->asArray()->findAll();
            }
        }
        $data['agency_type_id'] = $this->user->agency_type_id;
        $data['fund_agency_id'] = $this->user->fund_agency_id;

        $data['districts'] = [];

    }

    public function search() {
        if(!$this->request->isAJAX()){
            return $this->response->setStatusCode(404);
        }

        $txnModel = new TransactionModel();
        $requestData = $_REQUEST;
        $totalData = $txnModel->getTotal(['user_id'=>$this->user->user_id]);
        $totalFiltered = $totalData;

        $filter_search = $requestData['search']['value'];

        $order_columns = array(
            't.id','t.month','t.year','t.date_added','t.txn_type','t.agency_type_id'
        );
        $filter_data = array(
            'user_id' => $this->user->user_id,
            'filter_search' => $filter_search,
            'order' => $requestData['order'][0]['dir'],
            'sort' => $order_columns[$requestData['order'][0]['column']],
            'start' => $requestData['start'],
            'limit' => $requestData['length']
        );
//        $totalFiltered = 0;
        $totalFiltered = $txnModel->getTotal($filter_data);

        $filteredData = $txnModel->getAll($filter_data);

        $datatable=array();

        foreach($filteredData as $result) {

            $action  = '<div class="btn-group">';
            $action .= '<a class="btn btn-sm btn-primary" href="' . Url::transactionEdit.'/'.$result->id . '"><i class="fa fa-pencil"></i></a>';
            $action .= '<a class="btn btn-sm btn-danger btn-delete" href="' . Url::transactionDelete.'/'.$result->id . '"><i class="fa fa-trash"></i></a>';
            $action .= '</div>';

            if($result->status==0){
                $status = '<label class="badge badge-warning">'.$this->statuses[$result->status].'</label>';
            }
            if($result->status==1){
                $status = '<label class="badge badge-success">'.$this->statuses[$result->status].'</label>';
            }
            if($result->status==2){
                $status = '<label class="badge badge-danger">'.$this->statuses[$result->status].'</label>';
            }

            if($result->transaction_type=='expense'){
                $txn_type = '<label class="badge badge-danger">Expense</label>';
            } else {
                $txn_type = '<label class="badge badge-success">Fund Receipt</label>';
            }

            $datatable[]=array(
                $result->id,
                $result->month,
                $result->year,
                ymdToDmy($result->date_added),
                $txn_type,
                $result->agency_type,
                $result->block,
                $result->phy,
                $result->fin,
                $status,
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
        ob_end_clean();
        return $this->response->setJSON($json_data);

    }

    public function upload() {
        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,application/vnd.ms-excel]',
                'max_size[file,1024]',
                'ext_in[file,xls]',
            ]
        ]);

        if (!$input) {
            return $this->response->setJSON([
                'status'=>false,
                'message'=>'Invalid file',
                'errors'=>$this->validator->getErrors()
            ]);
        } else {
            $file = $this->request->getFile('file');

            $filename = strtolower($file->getName());

            //validate file name

            if(strpos($filename,'soe')===false && strpos($filename,'fund')===false){
                $invalid_filename = [
                    'status'=>false,
                    'message'=>'This is not a valid file',
                    'errors'=> []
                ];
                return $this->response->setJSON($invalid_filename);
            }

            $file->move(DIR_UPLOAD.'/transactions');
            $_file = DIR_UPLOAD.'/transactions/'.$file->getName();

            $reader = IOFactory::createReader('Xls');

            $spreadsheet = $reader->load($_file);

            $activesheet = $spreadsheet->getSheet(0);

            $row_data = $activesheet->toArray();

            $month = (int)$row_data[0][0];
            $year = (int)$row_data[0][1];
            $txn_type = $row_data[0][2];
            $fund_agency_id = $row_data[0][3];
            $agency_type_id = $row_data[0][4];
            $txn_type_text = $txn_type=='expense' ? 'Expenditure':'Fund Receipt';

            $txnModel = new TransactionModel();
            $txn = $txnModel
                ->where([
                    'block_id'=>$this->user->block_id,
                    'agency_type_id'=>$agency_type_id,
                    'month' => $month,
                    'year' => $year,
                    'transaction_type' => $txn_type,
                    'fund_agency_id' => $fund_agency_id,
                ])->first();

            $error = false;
            if($txn){
                $data = [
                    'status' => false,
                    'message' => $txn_type_text.' for the month already exists'
                ];
                $error = true;
                unlink($_file);
            }
            //check if date is open
            $upload_model = new AllowuploadModel();
            if($upload_model){

            }
            if(!$error){
                $txn_data = [
                    'block_id'=>$this->user->block_id,
                    'district_id'=>$this->user->district_id,
                    'agency_type_id'=>$agency_type_id,
                    'month' => $month,
                    'year' => $year,
                    'filename' => $file->getName(),
                    'status' => (int)in_array($this->user->agency_type_id,$this->settings->auto_approve_users),
                    'date_added' => date('Y-m-d'),
                    'user_id' => $this->user->user_id,
                    'transaction_type' => $txn_type,
                    'fund_agency_id' => $fund_agency_id
                ];
                $txn_id = $txnModel->insert($txn_data);

                //skip rows
                $row_data = array_slice($row_data, 6);
                $components = [];
                foreach ($row_data as $row) {
                    if($row[0]){
                        $components[] = [
                            'transaction_id' => $txn_id,
                            'component_id' => (int)$row[0],
                            'physical' => (int)$row[8],
                            'financial' => (float)$row[9],
                        ];
                    }
                }
                $tcModel = new TransactionComponentModel();
                $tcModel->insertBatch($components);

                $data = ['status'=>true];
            }
            ob_end_clean();
            return $this->response->setJSON($data);
        }
    }

    public function edit() {
        $txnModel = new TransactionModel();
        $txnCompModel = new TransactionComponentModel();
        $data = [];

        if($this->request->getMethod(1)=='POST'){
            $id = $this->uri->getSegment(4);

            $txn = $txnModel->find($this->uri->getSegment(4));
            $txnModel->delete($id);

            $txn_data = [
                'block_id'=>$txn->block_id,
                'district_id'=>$txn->district_id,
                'agency_type_id'=>$txn->agency_type_id,
                'month' => $txn->month,
                'year' => $txn->year,
                'filename' => $txn->filename,
                'status' => (int)in_array($this->user->agency_type_id,$this->settings->auto_approve_users),
                'date_added' => date('Y-m-d'),
                'user_id' => $txn->user_id,
                'transaction_type' => $txn->transaction_type,
                'fund_agency_id' => $txn->fund_agency_id
            ];
            $txn_id = $txnModel->insert($txn_data);

            //delete the existing transaction components
            $txnCompModel->where(['transaction_id'=>$id])->delete();
            $components = [];

            foreach ($this->request->getPost() as $component_id => $value) {
                $components[] = [
                    'transaction_id' => $txn_id,
                    'component_id' => $component_id,
                    'physical' => $value['phy'],
                    'financial' => $value['fin']
                ];
            }
            $txnCompModel->insertBatch($components);

            $this->session->setFlashdata('message','Your changes have been saved');
            return redirect()->to(Url::transaction);
        }

        $txn = new \stdClass();
        if ($this->uri->getSegment(4) && ($this->request->getMethod(true) != 'POST')) {
            $txn = $txnModel->find($this->uri->getSegment(4));

            if(!$txn){
                $this->session->setFlashdata('message','Transaction not found!');
                return redirect()->to(Url::transaction);
            }

            //validate user transaction
            if(!$txnModel->canEdit($txn)){
                $this->session->setFlashdata('message','You cannot edit this transaction');
                return redirect()->to(Url::transaction);
            }
        }

        if($txn->status==0 || $txn->status==2){
            $action = 'edit';
            $data['show_form'] = true;
        } else {
            $action = 'view';
            $data['show_form'] = false;
        }

        //editing override for atma
        if($this->user->agency_type_id != $this->settings->block_user){
            $action = 'edit';
            $data['show_form'] = true;
        }

        $data['block'] = $txn->block;
        $data['district'] = $txn->district;
        $data['agency_type'] = (new UserGroupModel())->find($txn->agency_type_id)->name;
        $data['fund_agency'] = $txn->fund_agency_id ? (new CommonModel())->getFundAgency($txn->fund_agency_id)['name']:'-';
        $data['month'] = getMonthById($txn->month)['name'];
        $data['year'] = getYear($txn->year);
        $data['date_added'] = ymdToDmy($txn->date_added);
        $data['phy'] = $txn->physical;
        $data['fin'] = $txn->financial;

        $data['status'] = $this->statuses[$txn->status];

        $data['remarks'] = $txn->remarks;

        $data['txn_type_text'] = $txn->transaction_type=='expense'?'Expense':'Fund Receipt';

        $filter = [
            'user_id' => $txn->user_id,
            'block_id' => $txn->block_id,
            'district_id' => $txn->district_id,
            'month' => $txn->month,
            'year' => $txn->year,
            'agency_type_id' => $txn->agency_type_id,
            'user_group' => $txn->agency_type_id,
            'fund_agency_id' => $txn->agency_type_id,
        ];
        $user = (new UserModel())->find($txn->user_id);
        $filter['fund_agency_id'] = $user->fund_agency_id;

        if($txn->agency_type_id == $this->settings->block_user
            || $txn->agency_type_id == $this->settings->cbo_user){
            $filter['component_agency_type_id'] = 5; //fa/cbo --to be added to settings
            $filter['category'] = 'program';
        }
        if($txn->agency_type_id == $this->settings->district_user){
            $filter['component_agency_type_id'] = 7; //fa/cbo --to be added to settings
            //for dmf angul/keunjhar
            if(in_array($this->user->district_id,[7,15])){
                $filter['component_agency_type_id'] = 7;
                $filter['category'] = ['program', 'pmu','addl'];
            }
        }
        if($txn->agency_type_id == $this->settings->ps_user){
            $filter['component_agency_type_id'] = 8; //ps --to be added to settings
        }
        if($txn->agency_type_id == $this->settings->rs_user){
            $filter['component_agency_type_id'] = 9; //rs --to be added to settings
        }

        $block_components = $txnModel->getBlockDistrictReport($filter);

        $components = $this->buildTree($block_components, 'parent', 'scomponent_id');

        $data['components'] = $this->getTable($components,$txn->transaction_type,$action);

        return $this->template->view('Admin\Transaction\Views\edit', $data);
    }

    public function downloadTemplate() {

        $month_id = $this->request->getGet('month');

        $year = $this->request->getGet('year');

        $txn_type = $this->request->getGet('txn_type');

        $block_id = $this->user->block_id;
        if($this->request->getGet('block_id')) {
            $block_id = $this->request->getGet('block_id');
        }

        $district_id = $this->user->district_id;
        if($this->request->getGet('district_id')) {
            $district_id = $this->request->getGet('district_id');
        }

        $agency_type_id = $this->user->agency_type_id;
        if($this->request->getGet('agency_type_id')) {
            $agency_type_id = $this->request->getGet('agency_type_id');
        }

        if(!$txn_type){
            $this->session->setFlashdata('message','Invalid request!!');
            return redirect()->to(Url::transaction);
        }

        $reader = IOFactory::createReader('Xls');
        $template_file = DIR_TEMPLATE.'soe_fund_receipt.xls';

        $spreadsheet = $reader->load($template_file);

        $activesheet = $spreadsheet->setActiveSheetIndex(0);

        $month = getMonthById($month_id)['name'];
        $fin_year = getYear($year);

//        $agency_type_id = $this->user->agency_type_id;
        $data = [
            'month' => $month_id,
            'year' => $year,
            'month_year' => $month.' '.$fin_year,
            'agency_type_id' => $agency_type_id,
            'txn_type' => $txn_type,
            'district_id' => $district_id,
            'block_id' => $block_id,
            'fund_agency_id' => $this->user->fund_agency_id,
        ];
        $activesheet->setTitle('Sheet1');

        $this->fillExcel($activesheet,$data);

        $writer = new Xls($spreadsheet);
        $txn_type_filename = $txn_type=='expense' ? 'SOE':'Fund_receipt';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$txn_type_filename . '_' . $this->user->username . '_' . $month . '-' . $year . '-' . date('Y-m-d His') . '.xls');
        ob_end_clean();
        $writer->save("php://output");

    }

    protected function fillExcel(&$activesheet, $data)
    {

        $year = $data['year'];
        $month = $data['month'];
        $month_year = $data['month_year'];

        $bM = new BlockModel();
        $block = $bM->find($data['block_id']);

        $dM = new DistrictModel();
        $district = $dM->find($data['district_id']);

        if ($data['txn_type'] == 'expense') {
            $txn_type_text = 'Expenditure';
        } else {
            $txn_type_text = 'Fund Receipt';
        }

        $activesheet->setCellValue($this->cells['heading'], $txn_type_text);
        $activesheet->setCellValue($this->cells['transaction_type'], $data['txn_type']);
        $activesheet->setCellValue($this->cells['month'], $month);
        $activesheet->setCellValue($this->cells['year'], $year);
        $activesheet->setCellValue($this->cells['fund_agency_id'], $data['fund_agency_id']);
        $activesheet->setCellValue($this->cells['agency_type_id'], $data['agency_type_id']);
        if ($block) {
            $activesheet->setCellValue($this->cells['level'], 'Block');
            $activesheet->setCellValue($this->cells['block'], $block->name);
        } else if ($district) {
            $activesheet->setCellValue($this->cells['level'], 'District');
            $activesheet->setCellValue($this->cells['block'], $district->name);
        }
        $activesheet->setCellValue($this->cells['monthyear'], $month_year);
        $activesheet->setCellValue($this->cells['agency'], $this->user->firstname);
        $activesheet->setCellValue($this->cells['prev_cell'], $txn_type_text . ' up to prev month');
        $activesheet->setCellValue($this->cells['during_cell'], $txn_type_text . ' during the month');
        $activesheet->setCellValue($this->cells['cum_cell'], 'Cumulative ' . $txn_type_text . ' till month');

        $txnModel = new TransactionModel();

        $filter = [
            'month' => $month,
            'year' => $year,
            'user_id' => $this->user->user_id,
            'user_group' => $this->user->agency_type_id
        ];

        //added by niranjan
        $agency_type_id = $filter['agency_type_id'] = $data['agency_type_id'];

        if ($agency_type_id == $this->settings->block_user || $txn->agency_type_id == $this->settings->cbo_user) {
            $filter['component_agency_type_id'] = [5, 6, 7]; //fa/cbo --to be added to settings
            $filter['category'] = 'program';
        }
        if ($agency_type_id == $this->settings->district_user) {
            $filter['component_agency_type_id'] = [5, 6, 7]; //fa/cbo --to be added to settings
        }
        if ($agency_type_id == $this->settings->ps_user) {
            $filter['component_agency_type_id'] = 8; //ps --to be added to settings
        }
        if ($agency_type_id == $this->settings->rs_user) {
            $filter['component_agency_type_id'] = 9; //rs --to be added to settings
        }

        if ($block) {
            $filter['block_id'] = $block->id;
        } else {
            $filter['block_id'] = null;
        }

        if ($district) {
            $filter['district_id'] = $district->id;
        } else {
            $filter['district_id'] = null;
        }
        $filter['fund_agency_id'] = $this->user->fund_agency_id;

        $block_components = $txnModel->getBlockDistrictReport($filter);

        $components = $this->buildTree($block_components, 'parent', 'component_id');
        //printr($components);
        //exit;
        $row = 6;

        $this->tot_ob_phy = $this->tot_ob_fin = $this->tot_upto_phy = $this->tot_upto_fin = 0;

        $this->i_cells = [];
        $this->j_cells = [];
        $this->k_cells = [];
        $this->l_cells = [];

        $this->fillComponents($components, $row, $activesheet);

        // total
        $i_cells = implode(',', $this->i_cells);
        $j_cells = implode(',', $this->j_cells);
        $k_cells = implode(',', $this->k_cells);
        $l_cells = implode(',', $this->l_cells);

        $activesheet->setCellValue("C$row", 'Grand Total for all components ');
        $activesheet->setCellValue("E$row", $this->tot_ob_phy);
        $activesheet->setCellValue("F$row", $this->tot_ob_fin);
        $activesheet->setCellValue("G$row", $this->tot_upto_phy);
        $activesheet->setCellValue("H$row", $this->tot_upto_fin);
        $activesheet->setCellValue("I$row", "=SUM($i_cells)");
        $activesheet->setCellValue("J$row", "=SUM($j_cells)");
        $activesheet->setCellValue("K$row", "=SUM($k_cells)");
        $activesheet->setCellValue("L$row", "=SUM($l_cells)");
        $activesheet->getStyle("B$row:L$row")
            ->applyFromArray(ExcelStyles::heading2())
            ->applyFromArray(ExcelStyles::fill_yellow());

    }

    protected function fillComponents($components, &$row, &$activesheet)
    {

        $this->ob_phy = $this->ob_fin = $this->upto_phy = $this->upto_fin = 0;

        //row start
        $row_start = $row;
        foreach ($components as $component) {

            if ($component['row_type'] == 'heading') {
                $activesheet->setCellValue("B$row", $component['number']);
                $activesheet->setCellValue("C$row", $component['description']);

                $activesheet->getStyle("B$row:C$row")
                    ->applyFromArray(ExcelStyles::heading2());
            } else {
                $activesheet->setCellValue("A$row", $component['component_id']);
                $activesheet->setCellValue("B$row", $component['number']);
                $activesheet->setCellValue("C$row", $component['description']);
                $activesheet->setCellValue("D$row", $component['agency_type']);
                $activesheet->setCellValue("E$row", $component['ob_phy']);
                $activesheet->setCellValue("F$row", $component['ob_fin']);
                $activesheet->setCellValue("G$row", $component['exp_upto_phy']);
                $activesheet->setCellValue("H$row", $component['exp_upto_fin']);
                $activesheet->setCellValue("I$row", '');
                $activesheet->setCellValue("J$row", '');
                $activesheet->setCellValue("K$row", "=G$row+I$row");
                $activesheet->setCellValue("L$row", "=H$row+J$row");

                //sub total
                $this->ob_phy += $component['ob_phy'];
                $this->ob_fin += $component['ob_fin'];
                if ($this->request->getGet('txn_type') == 'expense') {
                    $this->upto_phy += $component['exp_upto_phy'];
                    $this->upto_fin += $component['exp_upto_fin'];
                } else {
                    $this->upto_phy += $component['fr_upto_phy'];
                    $this->upto_fin += $component['fr_upto_fin'];
                }
                //total
                $this->tot_ob_phy += $component['ob_phy'];
                $this->tot_ob_fin += $component['ob_fin'];

                if ($this->request->getGet('txn_type') == 'expense') {
                    $this->tot_upto_phy += $component['exp_upto_phy'];
                    $this->tot_upto_fin += $component['exp_upto_fin'];
                } else {
                    $this->tot_upto_phy += $component['fr_upto_phy'];
                    $this->tot_upto_fin += $component['fr_upto_fin'];
                }

                $this->i_cells[] = 'I' . $row;
                $this->j_cells[] = 'J' . $row;
                $this->k_cells[] = 'K' . $row;
                $this->l_cells[] = 'L' . $row;
            }

            $row++;
            if (!empty($component['children'])) {
                $this->fillComponents($component['children'], $row, $activesheet);

                $activesheet->setCellValue("B$row", 'Sub Total');
                $activesheet->setCellValue("E$row", $this->ob_phy);
                $activesheet->setCellValue("F$row", $this->ob_fin);
                $activesheet->setCellValue("G$row", $this->upto_phy);
                $activesheet->setCellValue("H$row", $this->upto_fin);
                $last_row = $row - 1;
                $activesheet->setCellValue("I$row", "=SUM(I$row_start:I$last_row)");
                $activesheet->setCellValue("J$row", "=SUM(J$row_start:J$last_row)");
                $activesheet->setCellValue("K$row", "=G$row+I$row");
                $activesheet->setCellValue("L$row", "=H$row+J$row");

                $activesheet->getStyle("B$row:L$row")
                    ->applyFromArray(ExcelStyles::heading2())
                    ->applyFromArray(ExcelStyles::fill_yellow());

                $row++;
                $row_start = $row;
            }
        }
    }

    public function add() {

        $txnModel = new TransactionModel();
        $txnCompModel = new TransactionComponentModel();

        $block_id = $this->user->block_id;
        if($this->request->getGet('block_id')) {
            $block_id = $this->request->getGet('block_id');
        }
        $district_id = $this->user->district_id;
        if($this->request->getGet('district_id')) {
            $district_id = $this->request->getGet('district_id');
        }

        if($this->user->agency_type_id == $this->settings->district_user){
            $district_id = $this->user->district_id;
        }

        $fund_agency_id=$this->user->fund_agency_id;
        if($this->request->getGet('fund_agency_id')) {
            $fund_agency_id = $this->request->getGet('fund_agency_id');
        }

        $agency_type_id = $this->user->agency_type_id;

        //block and district can enter for cbo
        if ($this->user->agency_type_id == $this->settings->block_user
            || $this->user->agency_type_id == $this->settings->district_user) {
            if($this->request->getGet('agency_type_id')) {
                $agency_type_id = $this->request->getGet('agency_type_id');
            }
        }

        $txn_type = $this->request->getGet('txn_type');
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        if(!$txn_type){
            $this->session->setFlashdata('message','Invalid request!!');
            return redirect()->to(Url::transaction);
        }

        $data = [];

        //validate if transaction exists
        $txn = $txnModel->where([
            'block_id'=>$block_id,
            'district_id'=>$district_id,
            'agency_type_id'=>$agency_type_id,
            'month' => $month,
            'year' => $year,
            'user_id' => $this->user->user_id,
            'transaction_type' => $txn_type,
            'fund_agency_id' => $fund_agency_id
        ])->first();

        if($txn){
            $this->session->setFlashdata('message','Cannot add transaction. Transaction already exists!!');
            return redirect()->to(Url::transaction);
        }

        $filter = [
            'agency_type' => $this->user->agency_type_id,
            'block_id' => $block_id,
            'year' => $year,
            'month' => $month,
            'fund_agency_id' => $fund_agency_id,
        ];
        if ($this->user->agency_type_id == $this->settings->district_user) {
            $filter['district_id'] = $this->user->district_id;
        }

        //skip validation for ps
        if (in_array($this->user->agency_type_id,
            [$this->settings->ps_user,
                $this->settings->rs_user,11])) {
            //pass

        } else {
            $pending_transactions = $txnModel->pendingUploads($filter);

            if (isset($pending_transactions->total)) {
                if ($pending_transactions->total < ($month - 1)) {
                    $this->session->setFlashdata('message', 'Cannot add transaction. Please check for pending closing balance of the previous months!!');
                    return redirect()->to(Url::transaction);
                }
            } else {
                if ($pending_transactions['block_cbs']) {
                    foreach ($pending_transactions['block_cbs'] as $block_cb) {
                        if ($block_cb->total != ($month - 1)) {
                            $this->session->setFlashdata('message', 'Cannot add transaction. Block level uploads are pending in the previous months!!');
                            return redirect()->to(Url::transaction);
                        }
                    }
                }
                if ($pending_transactions['district_cbs'] < ($month - 1)) {
                    $this->session->setFlashdata('message', 'Cannot add transaction. Please check for pending uploads in the previous months!!');
                    return redirect()->to(Url::transaction);
                }
                if ($pending_transactions['pending_cbs']) {
                    $this->session->setFlashdata('message', 'Cannot add transaction. Blocks status are pending');
                    return redirect()->to(Url::transaction);
                }
            }
        }



        if($this->request->getMethod(1)=='POST'){

            $txn_data = [
                'block_id'=>$block_id,
                'district_id'=>$district_id,
                'agency_type_id'=>$agency_type_id,
                'month' => $month,
                'year' => $year,
                'filename' => '',
                'status' => (int)in_array($this->user->agency_type_id,$this->settings->auto_approve_users),
                'date_added' => date('Y-m-d'),
                'user_id' => $this->user->user_id,
                'transaction_type' => $txn_type,
                'fund_agency_id' => $fund_agency_id
            ];
            $txn_id = $txnModel->insert($txn_data);

            $components = [];

            foreach ($this->request->getPost() as $component_id => $value) {
                $components[] = [
                    'transaction_id' => $txn_id,
                    'component_id' => $component_id,
                    'physical' => $value['phy'],
                    'financial' => $value['fin']
                ];
            }
            $txnCompModel->insertBatch($components);

            $this->session->setFlashdata('message','New transaction added');
            return redirect()->to(Url::transaction);
        }

        $data['show_form'] = true;

        $data['block'] = $block_id ? (new BlockModel)->find($block_id)->name:'-';
        $data['district'] = $district_id ? (new DistrictModel)->find($district_id)->name:'-';
        $data['agency_type'] = $agency_type_id ? (new UserGroupModel)->find($agency_type_id)->name:'-';
        $data['fund_agency'] = $fund_agency_id ? (new CommonModel())->getFundAgency($fund_agency_id)['name']:'-';
        $data['month'] = getMonthById($month)['name'];
        $data['year'] = getYear($year);
        $data['date_added'] = date('Y/m/d');
        $data['phy'] = '-';
        $data['fin'] = '-';

        $data['status'] = '-';

        $data['remarks'] = '-';

        $data['txn_type_text'] = $txn_type=='expense'?'Expense':'Fund Receipt';

        $filter = [
            'user_id' => $this->user->user_id,
            'block_id' => $block_id,
            'district_id' => $this->user->district_id,
            'month' => $month,
            'year' => $year,
        ];

        if($district_id){
            $filter['district_id'] = $district_id;
        }
        if(in_array($agency_type_id,[$this->settings->ps_user,$this->settings->rs_user,11])) {
            $filter['component_agency_type_id'] = $agency_type_id;
        }
        if($agency_type_id==$this->settings->district_user){
            $filter['component_agency_type_id'] = 7;
            $filter['category'] = ['program', 'pmu'];
            //for dmf angul/keunjhar
            if(in_array($this->user->district_id,[7,15])){
                $filter['component_agency_type_id'] = 7;
                $filter['category'] = ['program', 'pmu','addl'];
            }
        }
        if($agency_type_id == $this->settings->block_user || $agency_type_id == $this->settings->cbo_user){
            $filter['component_agency_type_id'] = 5; //fa/cbo --to be added to settings
            $filter['category'] = 'program';
        }

        $filter['fund_agency_id'] = $fund_agency_id;

        $block_components = $txnModel->getBlockDistrictReport($filter);

        foreach ($block_components as &$component) {
            $component['fr_mon_phy'] = '';
            $component['fr_mon_fin'] = '';
            $component['exp_mon_phy'] = '';
            $component['exp_mon_fin'] = '';
            $component['fr_cum_phy'] = $component['fr_upto_phy'];
            $component['fr_cum_fin'] = $component['fr_upto_fin'];
            $component['exp_cum_phy'] = $component['exp_upto_phy'];
            $component['exp_cum_fin'] = $component['exp_upto_fin'];
        }

        $components = $this->buildTree($block_components, 'parent', 'scomponent_id');

        $data['components'] = $this->getTable($components,$txn_type,'edit');

        return $this->template->view('Admin\Transaction\Views\edit', $data);
    }

    public function getForm() {


        return $this->template->view('Admin\Transaction\Views\edit', $data);
    }

    public function delete($id) {
        $txnModel = new TransactionModel();
        $txnCompModel = new TransactionComponentModel();

        $txnModel->delete($id);
        $txnCompModel->where(['transaction_id'=>$id])->delete();

        $this->session->setFlashdata('message','The record has been deleted.');
        return redirect()->to(Url::transaction);

    }

    public function misIsUploaded() {

        $upload_allowed = true;
        if(env('soe.uploadDateValidation')) {
            $upload_model = new AllowuploadModel();

            $upload_allowed = $upload_model->uploadAllowed([
                'month' => $this->request->getGet('month'),
                'year' => $this->request->getGet('year'),
                'user_id' => $this->user->user_id,
                'agency_type_id' => $this->user->agency_type_id
            ]);
        }

        $misModel = new MISModel();
        $txn = $misModel->where([
            'block_id' => $this->user->block_id,
            'district_id' => $this->user->district_id,
            'agency_type_id' => $this->user->agency_type_id,
            'month' => $this->request->getGet('month'),
            'year' => $this->request->getGet('year'),
            'user_id' => $this->user->user_id,
        ])->first();

        if(!$upload_allowed) {
            $data['html'] = '<div class="col-12" id="alert-msg">
                        <div class="alert alert-danger" role="alert">
                            <p class="mb-0">The SoE/Fund Receipt upload is closed for the month.</p>
                        </div>
                        </div>';
        } else if(!$txn){
            $data['html'] = '<div class="col-12" id="alert-msg">
                        <div class="alert alert-danger" role="alert">
                            <p class="mb-0">Please upload MIS first to enable SoE/Fund Receipt upload. </p>
                        </div>
                        </div>';
        } else {
            $data['html'] = '<div class="col-md-3 upload-btn">
                            <button id="btn-download" class="btn btn-outline btn-primary"><i class="fa fa-download"></i> Download Template</button>
                        </div>
                        <div class="col-md-4 upload-btn">
                            <form class="dm-uploader" id="uploader">
                                <div role="button" class="btn btn-outline btn-warning">
                                    <i class="fa fa-folder-o fa-fw"></i> Upload Excel
                                    <input type="file" title="Click to add Files">
                                </div>
                                <small class="ml-3 status text-muted">Select a file...</small>
                            </form>
                        </div>
                        <div class="col-md-2 upload-btn">
                            <button id="btn-add" class="btn btn-outline btn-primary"><i class="fa fa-table"></i> Add New</button>
                        </div>';
        }

        return $this->response->setJSON($data);

    }

}
