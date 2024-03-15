<?php 
namespace Admin\Reports\Controllers;
use Admin\Common\Models\CommonModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Models\MPRUploadModel;
use Admin\Reports\Models\ReportsModel;
use Admin\Transaction\Models\TransactionModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;
use App\Controllers\AdminController;
use App\Libraries\Export;
use App\Traits\ReportTrait;
use App\Traits\TreeTrait;
use Config\Url;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Mpr extends AdminController
{
    private $block_model;
    use TreeTrait,ReportTrait {
        ReportTrait::generateTable insteadof TreeTrait;
        ReportTrait::getTable insteadof TreeTrait;
    }

    public function index($action=''){
        $this->block_model = new BlockModel();
        $data=[];

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }

        $data['month_id'] = getMonthIdByMonth(date('m'));
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }

        $data['districts'] = [];

        $data['fund_agency_id'] = $this->user->fund_agency_id?:1;
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
            $data['districts'] = [];
        }

        $data['agency_type_id'] = $this->user->agency?$this->user->agency_type_id:0;
        if($this->user->agency_type_id==$this->settings->district_user){
            $data['agency_type_id'] = 0;
        }
        if($this->user->agency_type_id==$this->settings->block_user){
            $data['agency_type_id'] = 0;
        }

        $data['user_group_id'] = $this->user->agency_type_id;


        $data['district_id'] = $this->user->district_id;
        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
            $data['user_group_id']=7;
        }

        $data['block_id'] = $this->user->block_id;
        if($this->request->getGet('block_id')){

            $data['block_id'] = $this->request->getGet('block_id');
            $data['user_group_id']=[6,5];
        }

        if($this->request->getGet('agency_type_id')){
            $data['agency_type_id'] = $data['user_group_id']=$this->request->getGet('agency_type_id');
        }

        $component_agency=array_column((new UserGroupModel())->getAgencyTree([
            'fund_agency_id'=>$data['fund_agency_id'],
            'user_group_id'=>$data['user_group_id'],
            'agency_type_id'=>$data['agency_type_id']
        ]),'user_group_id');

        if($data['user_group_id']==11){
            $fund_receipt_agency=array_column((new UserGroupModel())->getAgencyChild([
                'fund_agency_id'=>$data['fund_agency_id'],
                'user_group_id'=>$data['user_group_id']
            ]),'user_group_id');
        }else{
            $fund_receipt_agency=$data['user_group_id'];
        }

        //hard code for block user
        if($this->user->agency_type_id==$this->settings->block_user
            && $this->request->getGet('agency_type_id')==''){
            $component_agency = [5,6]; //block user --fa and cbo
            $fund_receipt_agency = [5,6]; //block user --fa and cbo
        }

        // printr( $data['component_agency']);

        $filter = [
            'month_id' => $data['month_id'],
            'year_id' => $data['year_id'],
            'user_id' => $this->user->id,
            'agency_type_id'=>$data['agency_type_id'],
            'district_id'=>$data['district_id'],
            'block_id'=>$data['block_id'],
            'fund_agency_id'=>$data['fund_agency_id'],
            'user_group_id'=>$data['user_group_id'],
            'component_agency'=>(array)$component_agency,
            'fundreceipt_agency'=>(array)$fund_receipt_agency
        ];

        // printr($filter);

        $reportModel = new ReportsModel();

        $data['components'] = [];
        $this->filterPanel($data);

        $components = $reportModel->getMpr($filter);

        $components = $this->buildTree($components, 'parent', 'scomponent_id');

        if($action=='download') {
            $data['components'] = $this->getTable($components, 'download');
        } else {
            $data['components'] = $this->getTable($components, 'view');
        }

        //mpr table html for excel and view --rakesh --092/06/23
        $data['mpr_table'] = view('Admin\Reports\Views\mpr_table', $data);

        $this->filterPanel($data);

        if($data['district_id']) {
            $data['district'] = (new DistrictModel())->find($data['district_id'])->name;
            $data['blocks'] = $this->block_model->where(
                [
                    'district_id' => $data['district_id'],
                    'fund_agency_id' => $data['fund_agency_id'],
                ]
            )->asArray()->findAll();

        }

        if($data['block_id']) {
            $block = $this->block_model->find($data['block_id']);
            $data['block'] = $block->name;

            $data['fund_agency'] = $block->fund_agency_id ? (new CommonModel())->getFundAgency($block->fund_agency_id)['name']:'-';
        }
        $data['month_name'] = getMonthById($data['month_id'])['name'];
        $data['fin_year'] = getYear($data['year_id']);

        if($action=='download'){
            $filename = 'MPR_' . $data['month_name'].$data['fin_year']. '_' . date('Y-m-d His') . '.xlsx';

            $spreadsheet=Export::createExcelFromHTML($data['mpr_table'],$filename,true);
            if($spreadsheet){
                $worksheet = $spreadsheet->getActiveSheet();
                $columnIndex = 'B'; // Change this to the desired column index
                $wordWrapCols=[
                    'G2','O2','Q1'
                ];
                foreach($wordWrapCols as $col){
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                // Get the highest row index in the column
                $highestRow = $worksheet->getHighestRow();

                // Apply word wrap to each cell in column B
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cell = $worksheet->getCell($columnIndex . $row);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                $worksheet->getColumnDimension($columnIndex)->setWidth(20);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'. $filename .'"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            exit;
        }

        $data['download_url'] = Url::mprDownload.'?year='.$data['year_id'].
            '&month='.$data['month_id'].'&district_id='.$data['district_id'].
            '&agency_type_id='.$data['agency_type_id'].
            '&block_id='.$data['block_id'].'&fund_agency_id='.$data['fund_agency_id'];

        return $this->template->view('Admin\Reports\Views\mpr_block', $data);

    }

    public function indexNew($action=''){
        $this->block_model = new BlockModel();
        $data=[];

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }

        $data['month_id'] = getMonthIdByMonth(date('m'));
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }

        $data['districts'] = [];

        $data['fund_agency_id'] = $this->user->fund_agency_id?:1;
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
            $data['districts'] = [];
        }

        $data['agency_type_id'] = $this->user->agency?$this->user->agency_type_id:0;
        if($this->user->agency_type_id==$this->settings->district_user){
            $data['agency_type_id'] = 0;
        }
        if($this->user->agency_type_id==$this->settings->block_user){
            $data['agency_type_id'] = 0;
        }

        $data['user_group_id'] = $this->user->agency_type_id;

        $data['district_id'] = $this->user->district_id;
        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
            $data['user_group_id']=7;
        }

        $data['block_id'] = $this->user->block_id;
        if($this->request->getGet('block_id')){

            $data['block_id'] = $this->request->getGet('block_id');
            $data['user_group_id']=[6,5];
        }

        if($this->request->getGet('agency_type_id')){
            $data['agency_type_id'] = $data['user_group_id']=$this->request->getGet('agency_type_id');
        }

        $component_agency=array_column((new UserGroupModel())->getAgencyTree([
            'fund_agency_id'=>$data['fund_agency_id'],
            'user_group_id'=>$data['user_group_id'],
            'agency_type_id'=>$data['agency_type_id']
        ]),'user_group_id');

        if($data['user_group_id']==11){
            $fund_receipt_agency=array_column((new UserGroupModel())->getAgencyChild([
                'fund_agency_id'=>$data['fund_agency_id'],
                'user_group_id'=>$data['user_group_id']
            ]),'user_group_id');
        }else{
            $fund_receipt_agency=$data['user_group_id'];
        }

        //hard code for block user
        if($this->user->agency_type_id==$this->settings->block_user
            && $this->request->getGet('agency_type_id')==''){
            $component_agency = [5,6]; //block user --fa and cbo
            $fund_receipt_agency = [5,6]; //block user --fa and cbo
        }

        // printr( $data['component_agency']);

        $filter = [
            'month_id' => $data['month_id'],
            'year_id' => $data['year_id'],
            'user_id' => $this->user->id,
            'agency_type_id'=>$data['agency_type_id'],
            'district_id'=>$data['district_id'],
            'block_id'=>$data['block_id'],
            'fund_agency_id'=>$data['fund_agency_id'],
            'user_group_id'=>$data['user_group_id'],
            'component_agency'=>(array)$component_agency,
            'fundreceipt_agency'=>(array)$fund_receipt_agency
        ];

        // printr($filter);

        $reportModel = new ReportsModel();

        $data['components'] = [];
        $this->filterPanel($data);

        $components = $reportModel->getMpr($filter);

        $components = $this->buildTree($components, 'parent', 'scomponent_id');

        if($action=='download') {
            $data['components'] = $this->getTable($components, 'download');
        } else {
            $data['components'] = $this->getTable($components, 'view');
        }

        //mpr table html for excel and view --rakesh --092/06/23
        $data['mpr_table'] = view('Admin\Reports\Views\mpr_table', $data);

        $this->filterPanel($data);

        if($data['district_id']) {
            $data['district'] = (new DistrictModel())->find($data['district_id'])->name;
            $data['blocks'] = $this->block_model->where(
                [
                    'district_id' => $data['district_id'],
                    'fund_agency_id' => $data['fund_agency_id'],
                ]
            )->asArray()->findAll();

        }

        if($data['block_id']) {
            $block = $this->block_model->find($data['block_id']);
            $data['block'] = $block->name;

            $data['fund_agency'] = $block->fund_agency_id ? (new CommonModel())->getFundAgency($block->fund_agency_id)['name']:'-';
        }
        $data['month_name'] = getMonthById($data['month_id'])['name'];
        $data['fin_year'] = getYear($data['year_id']);

        if($action=='download'){
            $filename = 'MPR_' . $data['month_name'].$data['fin_year']. '_' . date('Y-m-d His') . '.xlsx';

            $spreadsheet=Export::createExcelFromHTML($data['mpr_table'],$filename,true);
            if($spreadsheet){
                $worksheet = $spreadsheet->getActiveSheet();
                $columnIndex = 'B'; // Change this to the desired column index
                $wordWrapCols=[
                    'G2','O2','Q1'
                ];
                foreach($wordWrapCols as $col){
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                // Get the highest row index in the column
                $highestRow = $worksheet->getHighestRow();

                // Apply word wrap to each cell in column B
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cell = $worksheet->getCell($columnIndex . $row);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                $worksheet->getColumnDimension($columnIndex)->setWidth(20);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'. $filename .'"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            exit;
        }

        $data['download_url'] = Url::mprDownload.'?year='.$data['year_id'].
            '&month='.$data['month_id'].'&district_id='.$data['district_id'].
            '&agency_type_id='.$data['agency_type_id'].
            '&block_id='.$data['block_id'].'&fund_agency_id='.$data['fund_agency_id'];

        return $this->template->view('Admin\Reports\Views\mpr_block', $data);

    }

    //created by Niranjan code
    public function abstractMpr($action='') {
        $this->block_model = new BlockModel();
        $data=[];

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }

        $data['month_id'] = getCurrentMonthId();
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }

        $data['fund_agency_id'] = $this->user->fund_agency_id?:1;
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }


        $data['agency_type_id'] = $this->user->agency?$this->user->agency_type_id:0;
        if($this->user->agency_type_id==$this->settings->district_user){
            $data['agency_type_id'] = 0;
        }
        if($this->user->agency_type_id==$this->settings->block_user){
            $data['agency_type_id'] = 0;
        }

        $data['user_group_id'] = $this->user->agency_type_id;


        $data['district_id'] = $this->user->district_id;
        if($this->request->getGet('district_id')){
            $data['district_id'] = $this->request->getGet('district_id');
            $data['user_group_id']=7;
        }

        $data['block_id'] = $this->user->block_id;
        if($this->request->getGet('block_id')){
            $data['block_id'] = $this->request->getGet('block_id');
            $data['user_group_id']=[6,5];
        }


        if($this->request->getGet('agency_type_id')){
            $data['agency_type_id'] = $data['user_group_id']=$this->request->getGet('agency_type_id');
        }

        $component_agency=array_column((new UserGroupModel())->getAgencyTree([
            'fund_agency_id'=>$data['fund_agency_id'],
            'user_group_id'=>$data['user_group_id'],
            'agency_type_id'=>$data['agency_type_id']
        ]),'user_group_id');

        if($data['user_group_id']==11){
            $fund_receipt_agency=array_column((new UserGroupModel())->getAgencyChild([
                'fund_agency_id'=>$data['fund_agency_id'],
                'user_group_id'=>$data['user_group_id']
            ]),'user_group_id');
        }else{
            $fund_receipt_agency=$data['user_group_id'];
        }


        // printr( $data['component_agency']);

        $filter = [
            'month_id' => $data['month_id'],
            'year_id' => $data['year_id'],
            'user_id' => $this->user->id,
            'agency_type_id'=>$data['agency_type_id'],
            'district_id'=>$data['district_id'],
            'block_id'=>$data['block_id'],
            'fund_agency_id'=>$data['fund_agency_id'],
            'user_group_id'=>$data['user_group_id'],
            'component_agency'=>(array)$component_agency,
            'fundreceipt_agency'=>(array)$fund_receipt_agency
        ];

        // printr($filter);

        $reportModel = new ReportsModel();

        $data['components'] = [];
        //$this->filterPanel($data);

        $components = $reportModel->getMpr($filter);

        $components = $this->buildTree($components, 'parent', 'scomponent_id');
        
        $components=$this->calculateAbstractSum($components);

        //rakesh
        if($action=='download'){
            $data['components'] = $this->getAbstarctTable($components,'download');
        } else {
            $data['components'] = $this->getAbstarctTable($components);
        }

        //mpr table html for excel and view --rakesh --092/06/23
        $data['mpr_table'] = view('Admin\Reports\Views\abstract_mpr_table', $data);

        if($data['district_id']) {
            $data['district'] = (new DistrictModel())->find($data['district_id'])->name;
            $data['blocks'] = $this->block_model->where(
                [
                    'district_id' => $data['district_id'],
                    'fund_agency_id' => $data['fund_agency_id'],
                ]
            )->asArray()->findAll();

        }

        if($data['block_id']) {
            $block = $this->block_model->find($data['block_id']);
            $data['block'] = $block->name;

            $data['fund_agency'] = $block->fund_agency_id ? (new CommonModel())->getFundAgency($block->fund_agency_id)['name']:'-';
        }
        $data['month_name'] = getMonthById($data['month_id'])['name'];
        $data['fin_year'] = getYear($data['year_id']);

        $this->filterPanel($data);

        if($action=='download'){
            $filename = 'Abstract_MPR_' . $data['month_name'].$data['fin_year']. '_' . date('Y-m-d His') . '.xlsx';

            $spreadsheet=Export::createExcelFromHTML($data['mpr_table'],$filename,true);
            if($spreadsheet){
                $worksheet = $spreadsheet->getActiveSheet();
                $columnIndex = 'B'; // Change this to the desired column index
                $wordWrapCols=[
                    'G2','O2','Q1'
                ];
                foreach($wordWrapCols as $col){
                    $cell = $worksheet->getCell($col);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                // Get the highest row index in the column
                $highestRow = $worksheet->getHighestRow();

                // Apply word wrap to each cell in column B
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cell = $worksheet->getCell($columnIndex . $row);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }

                $worksheet->getColumnDimension($columnIndex)->setWidth(20);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'. $filename .'"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            exit;
        }

        $data['download_url'] = Url::abstractMprDownload.'?year='.$data['year_id'].
            '&month='.$data['month_id'].'&district_id='.$data['district_id'].
            '&agency_type_id='.$data['agency_type_id'].
            '&block_id='.$data['block_id'].'&fund_agency_id='.$data['fund_agency_id'];

        return $this->template->view('Admin\Reports\Views\abstract_mpr', $data);
    }

    protected function filterPanel(&$data) {
        $data['years'] = getAllYears();
        $data['months'] = getAllMonths();
        $data['filter_panel'] = '';
        $data['get_block_url'] = Url::getBlocks;
        $data['get_district_url'] = Url::getDistricts;
        
        if($this->user->agency_type_id==$this->settings->district_user) {
            $filter = [
                'district_id' => $this->user->district_id,
            ];
            if($this->user->fund_agency_id){
                $filter['fund_agency_id'] = $this->user->fund_agency_id;
            }
            $data['agency_types']=(new UserGroupModel())->where('id',$this->settings->district_user)->asArray()->findAll();
            $data['blocks'] = $this->block_model->where($filter)->asArray()->findAll();
            $data['filter_panel'] = view('Admin\Reports\Views\district_filter_panel',$data);
        }

        if($this->user->agency_type_id==$this->settings->block_user){
            $data['agency_types']=(new UserGroupModel())->whereIn('id',[$this->settings->block_user,$this->settings->cbo_user])->asArray()->findAll();
            $data['filter_panel'] = view('Admin\Reports\Views\block_filter_panel',$data);
        }

        if($this->user->agency_type_id==$this->settings->ps_user){

//            $data['districts'] = (new DistrictModel())->asArray()->findAll();
//            $data['blocks'] = $this->block_model->where(['district_id' => $this->request->getGet('district_id')])->asArray()->findAll();
//            foreach ($this->settings->user_can_access as $user_group => $user_can_access_grp) {
//                if(8==$user_group){
//                    $data['agency_types'] = (new UserGroupModel())->whereIn('id',
//                        $user_can_access_grp)->orderBy('name')->asArray()->findAll();
//                }
//            }
            $data['fund_agency_id'] = 1;
            if($this->request->getGet('fund_agency_id')){
                $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
            }
            $data['fund_agencies'] = (new BlockModel())->getFundAgencies(['asObject'=>true]);
            
            $data['filter_panel'] = view('Admin\Reports\Views\ps_filter_panel',$data);
        }

        if($this->user->agency_type_id==11){ // spmu user

            $data['fund_agency_id'] = 1;
            if($this->request->getGet('fund_agency_id')){
                $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
            }

            $data['fund_agencies'] = (new BlockModel())->getFundAgencies(['asObject'=>true]);

            $data['agency_types'] = [''=>'All','8'=>'WASSAN','9'=>'NCDS'];

            $data['districts'] = (new DistrictModel())->getDistrictsByFundAgency($data['fund_agency_id']);

            $data['blocks'] = $this->block_model->where([
                'district_id' => $this->request->getGet('district_id'),
                'fund_agency_id' => $this->request->getGet('fund_agency_id'),
            ])->asArray()->findAll();
            
            $data['filter_panel'] = view('Admin\Reports\Views\state_filter_panel',$data);
        }

        if($this->user->isAdmin()){ // admin
            $data['fund_agency_id'] = 1;
            if($this->request->getGet('fund_agency_id')){
                $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
            }

            $data['fund_agencies'] = (new BlockModel())->getFundAgencies(['asObject'=>true]);

            $data['agency_types'] = [
                ''=>'All',
                '8'=>'WASSAN',
                '9'=>'NCDS'
            ];

            $data['districts'] = (new DistrictModel())->getDistrictsByFundAgency($data['fund_agency_id']);

            $data['blocks'] = $this->block_model->where([
                'district_id' => $this->request->getGet('district_id'),
                'fund_agency_id' => $this->request->getGet('fund_agency_id'),
            ])->asArray()->findAll();

            $data['filter_panel'] = view('Admin\Reports\Views\state_filter_panel',$data);
        }
    }

    public function uploadStatus(){

        $data['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $data['year_id'] = $this->request->getGet('year');
        }

        $data['month_id'] = getCurrentMonthId();
        if($this->request->getGet('month')){
            $data['month_id'] = $this->request->getGet('month');
        }

        $data['fund_agency_id']=0;
        if($this->request->getGet('fund_agency_id')){
            $data['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        $muModel = new MPRUploadModel();
        $data['mpruploadstatus'] = $muModel->getMPRByDistricts($data);

        return $this->template->view('Admin\Reports\Views\mpr_upload_status', $data);
    }

}
