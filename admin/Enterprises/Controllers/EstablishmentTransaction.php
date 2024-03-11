<?php

namespace Admin\Enterprises\Controllers;

use Admin\Dashboard\Models\GpsModel;
use Admin\Dashboard\Models\BlockModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Admin\Enterprises\Models\EnterprisesTransactionModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EstablishmentTransactionDetailsModel;
use Admin\Enterprises\Models\MonthModel;
use Admin\Dashboard\Models\YearModel;
use Admin\Enterprises\Models\EnterpriseUnitGroup;
use App\Controllers\AdminController;
use App\Libraries\Export;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Config\ExcelStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use Mpdf\Tag\Tr;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Node\Stmt\Label;

class EstablishmentTransaction extends AdminController
{
    // private $establishmenTransaction;
    private $enterpriseTxnsDtls;
    private $yearModel;
    private $monthModel;
    private $districtModel;
    private $entunitModel;
    private $entUnitGrpModel;
    private $enterprisesModel;
    private $enterpriseTxnModel;


    public function __construct()
    {
        // $this->establishmenTransaction = new EstablishmentTransactionModel();
        $this->enterpriseTxnsDtls = new EstablishmentTransactionDetailsModel();
        $this->yearModel = new YearModel();
        $this->monthModel = new MonthModel();
        $this->districtModel = new DistrictModel();
        $this->entunitModel = new EnterprisesUnitModel();
        $this->entUnitGrpModel = new EnterpriseUnitGroup();
        $this->entunitModel = new EnterprisesUnitModel();
        $this->enterprisesModel = new EnterprisesModel();
        $this->enterpriseTxnModel = new EnterprisesTransactionModel();

    }
    private $columns = [
        'Machinary' => [
            'no_of_days_functional' => ['label' => 'No. of Days Functional', 'rules' => 'number'],
            'total_turnover' => ['label' => 'Total turnover / sale value', 'rules' => 'decimal'],
            'produced' => ['label' => 'Quintals of Produce processed', 'rules' => 'decimal'],
            'total_expend' => ['label' => 'Total expenditure', 'rules' => 'decimal'],
            'under_maintenance' => ['label' => 'No. of times under maintenance', 'rules' => 'number'],
        ],
        'Food' => [
            'no_of_days_functional' => ['label' => 'No. of Days Functional', 'rules' => 'number'],
            'total_turnover' => ['label' => 'Total turnover / sale value', 'rules' => 'decimal'],
            'total_expend' => ['label' => 'Total expenditure', 'rules' => 'decimal'],
            'event_attend' => ['label' => 'No. of event attend', 'rules' => 'number'],
        ],
        'CHC' => [
            'farmer_user' => ['label' => 'NO. of farmer user', 'rules' => 'number'],
            'service_charge' => ['label' => 'Value of service charge collected (in rupees)', 'rules' => 'decimal'],
            'total_expend' => ['label' => 'Expenditure if any(rupees)', 'rules' => 'decimal'],
        ],
        'CMSC' => [
            'farmer_user' => ['label' => 'NO. of farmer user', 'rules' => 'number'],
            'seed_support' => ['label' => 'Quantity of seed supported (in quintals)', 'rules' => 'decimal'],
            'seed_store' => ['label' => 'Quantity of seed in store (in quintals)', 'rules' => 'decimal'],
            'service_charge' => ['label' => 'Value of service charges & sale of seed', 'rules' => 'decimal'],
            'total_expend' => ['label' =>  'Expenditure if any (in rupees)', 'rules' => 'decimal'],
        ],
    ];

    public function index()
    {
        helper('form');
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
        $data['years'][0] = 'Select Year';
        $data['year_id'] = 0;
        $years = $this->yearModel->findAll();
        foreach ($years as $year) {
            $data['years'][$year->id] = $year->name;
        }

        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }
        else{
            $data['year_id'] = $this->yearModel->getCurrentYear()->id;;

        }
        $data['months'][0] = 'Select Month';

        $months = $this->monthModel->findAll();

        foreach ($months as $month) {
            $data['months'][$month->id] = $month->name;
        }

        $data['month_id'] = 0;
        if ($this->request->getGet('month_id')) {
            $data['month_id'] = $this->request->getGet('month_id');
        }
        $data['districts'][0] = 'Select Districts';

        if ($this->user->district_id) {
            $districts = $this->districtModel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
        } else {
            $districts = $this->districtModel->orderBy('name', 'asc')->findAll();
        }

        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $data['district_id'] = $this->user->district_id;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }
        //unit
        $data['units'][0] = 'Select units';

        $units =  $this->entunitModel->orderBy('name', 'asc')->findAll();

        foreach ($units as $unit) {
            $data['units'][$unit->id] = $unit->name;
        }

        $data['unit_id'] = 0;
        if ($this->request->getGet('unit_id')) {
            $data['unit_id'] = $this->request->getGet('unit_id');
        }


        $data['period'] = 0;
        if ($this->request->getGet('period')) {
            $data['period'] = $this->request->getGet('period');
        }



        $data['trans'] = [];


        $data['datatable_url'] = admin_url('enterprisestrans/search');

        $data['excel_link'] = admin_url('enterprisestrans/download');
        $data['upload_url'] = admin_url('enterprisestrans/upload');
// dd($data);
        return $this->template->view('Admin\Enterprises\Views\establishmentTransaction', $data);
    }

    /**
     * Search function to filter and retrieve data based on user input.
     *
     * @param None
     * @throws None
     * @return JSON response with filtered data for DataTables.
     */
    public function search()
    {

        $requestData = $_REQUEST;
        $totalData = $this->enterpriseTxnsDtls->getTotals();


        $totalFiltered = $totalData;
        // This array use for filter data 
        $filter_data = array(
            'filter_search' => $requestData['search']['value'],
            'district_id' => $requestData['district_id'],
            'month_id' => $requestData['month_id'],
            'unit_id' => $requestData['unit_id'],
            'year_id' => $requestData['year_id'],
            'period' => $requestData['period'],
            'order' => $requestData['order'][0]['dir'],
            'sort' => $requestData['order'][0]['column'],
            'start' => $requestData['start'],
            'limit' => $requestData['length'],
        );

        $totalFiltered = $this->enterpriseTxnsDtls->getTotals($filter_data);
       
        $filteredData = $this->enterpriseTxnsDtls->periodswisetrans($filter_data);
        $datatable = array();
        foreach ($filteredData as $result) {

            $action = '<div class="btn-group btn-group-sm pull-right">';
            $action .=         '<a class="btn btn-sm btn-primary" href="' . admin_url('enterprisestrans/edit?id=' . $result->txn_id) . '"><i class="fa fa-pencil"></i></a>';

            $action .= '</div>';
            // result holds the database column name
            $datatable[] = array(
                $result->created_at,
                $result->unit_name,
                $result->district_name,
                $result->block_name,
                $result->gp_name,
                $result->village_name,
                $result->month_name,
                $result->year_name,
                $result->period,
                $action,
            );
        }

        $json_data = array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $datatable
        );

        return $this->response->setContentType('application/json')->setJSON($json_data);
    }

    private function filter()
    {
        $filter = [];

        // if $this user has district_id, then filter by district_id

        if ($this->request->getGet('district_id') > 0) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }
        if ($this->request->getGet('year_id') > 0) {
            $filter['year_id'] = $this->request->getGet('year_id');
        }
        if ($this->request->getGet('month_id') > 0) {
            $filter['month_id'] = $this->request->getGet('month_id');
        }
        if ($this->request->getGet('unit_id') > 0) {
            $filter['unit_id'] = $this->request->getGet('unit_id');
        }
        if ($this->request->getGet('period') >0) {
            $filter['period'] = $this->request->getGet('period');
        }

        $filteredData =  $this->enterpriseTxnsDtls->periodswisetrans($filter);
        return $filteredData;
    }

    public function edit()
    {

        if ($this->request->getMethod(1) == 'POST') {
            $id = $this->request->getGet('id');
            $enterprisetransdata = [];
            $entdata = $this->enterpriseTxnsDtls->idwisetrans($id);

            if (!empty($entdata) && isset($entdata[0])) {
                $entdata = $entdata[0];

                $unit_groups = $this->columns;

                foreach ($unit_groups as $key => $columns) {
                    if ($key == $entdata->unit_group_name) {

                        foreach ($columns as $columnName => $columnValue) {
                            $enterprisetransdata[$columnName] = $this->request->getPost($columnName);
                        }
                    }
                }
            }

            $this->enterpriseTxnsDtls->update($id, $enterprisetransdata);

            return redirect()->to(admin_url('enterprises/transaction'))->with('message', 'update successful');
        }


        return $this->getForm();
    }

    private function getForm()
    {
        helper('form');
        $data['unit_groups'] = $this->columns;
        $id = $this->request->getGet('id');
        if ($id) {
            $entdata = $this->enterpriseTxnsDtls->idwisetrans($id);
            if (!empty($entdata) && isset($entdata[0])) {
                $entdata = $entdata[0];

                $entranses = [
                    'year_name' => $entdata->year_name,
                    'unit_name' => $entdata->unit_name,
                    'unit_group_name' => $entdata->unit_group_name,
                    'district_name' => $entdata->district_name,
                    'block_name' => $entdata->block_name,
                    'month_name' => $entdata->month_name,
                    'gp_name' => $entdata->gp_name,
                    'village_name' => $entdata->village_name,
                    'period' => $entdata->period,
                    'created_at' => ymdToDmy($entdata->created_at) ?: 0,
                    'no_of_days_functional' => $entdata->no_of_days_functional ?: 0,
                    'produced' => $entdata->produced ?: 0,
                    'charges_per_qtl' => $entdata->charges_per_qtl ?: 0,
                    'total_expend' => $entdata->total_expend ?: 0,
                    'total_turnover' => $entdata->total_turnover ?: 0,
                    'under_maintenance' => $entdata->under_maintenance ?: 0,
                    'event_attend' => $entdata->event_attend ?: 0,
                    'farmer_user' => $entdata->farmer_user ?: 0,
                    'service_charge' => $entdata->service_charge ?: 0,
                    'seed_support' => $entdata->seed_support ?: 0,
                    'seed_store' => $entdata->seed_store ?: 0,
                ];

                $data['entranses'] = $entranses;
            }
            // dd($data);
            return $this->template->view('Admin\Enterprises\Views\editEstablishmentTransaction', $data);
        }
    }

    public function download()
    {
        $unit_groups = $this->entUnitGrpModel->findAll();

        $worksheet_unit = [];
        foreach ($unit_groups as $group) {
            $units = $this->entunitModel->getAll(['unit_group_id' => $group->id]);
            $group->units = $units;
        }

        $month_id = $this->request->getGet('month_id');
        $year_id = $this->request->getGet('year_id');
        $district_id = $this->request->getGet('district_id');
        $period = $this->request->getGet('period');
        $month_name = $this->monthModel->find($month_id)->name;
        $year_name = $this->yearModel->find($year_id)->name;
        $filename = 'EntTxnTemplate_' . $month_name . '_' . $year_name . '_' . date('Y-m-d_His') . '.xlsx';
        $sheetindex = 0;
        $reader = new Html();
        $doc = new \DOMDocument();
        $spreadsheet = new Spreadsheet();

        //loop through all unit groups --sheets
        foreach ($unit_groups as &$group) {

            // fetch enterprises by district_id and unit_id
            $total_enterprises = 0;
            foreach ($group->units as &$unit) {
                $enterprises = $this->enterprisesModel->getBy([
                    'district_id' => $district_id,
                    'unit_id' => $unit['id']
                ]);

                $unit['enterprises'] = $enterprises;
                $total_enterprises += count($enterprises);

                $group->columns = $this->columns[$unit['unit_group']];
            }
            $group->total_enterprises = $total_enterprises;
        }

        $data = [
            'month_id' => $month_id,
            'year_id' => $year_id,
            'district_id' => $district_id,
            'period' => $period
        ];

        $hasEnterprises = 0;
        foreach ($unit_groups as $groups) {

            $data['group_id'] = $groups->id;
            $data['units'] = [];
            if ($groups->total_enterprises > 0) {

                $worksheet = $spreadsheet->createSheet($sheetindex);
                $worksheet->setTitle($groups->name);

                $data['columns'] = $groups->columns;
                $data['group_name'] = $groups->name;

                foreach ($groups->units as $key => $unit) {
                    if ($unit['enterprises']) {
                        // heading title
                        $data['units'][$key]['heading_title'] = ucfirst($unit['name']);
                        $data['units'][$key]['enterprises'] = $unit['enterprises'];
                    }
                }

                $htmltable = view('Admin\Enterprises\Views\excelForm', $data);

                $htmltable = preg_replace("/&(?!\S+;)/", "&amp;", $htmltable);

                $reader->setSheetIndex($sheetindex);
                $spreadsheet = $reader->loadFromString($htmltable, $spreadsheet);

                $worksheet = $spreadsheet->getActiveSheet();

                // Load HTML content into a DOM object for formatting from class
                $doc->loadHTML($htmltable);

                $rows = $doc->getElementsByTagName('tr');

                // formatting and designing the columns and rows
                //Set auto-size column word wrap for all columns
                $highestColumn = $worksheet->getHighestColumn();
                if ($worksheet && $highestColumn) {
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $worksheet->getColumnDimension($col)->setWidth(20);
                        $worksheet->getColumnDimension($col)->setAutoSize(false);
                        $worksheet->getStyle($col)->getAlignment()->setWrapText(true);
                    }
                }

                //set column custom widths
                $worksheet->getColumnDimension('J')->setWidth(30);
                $worksheet->getColumnDimension('C')->setWidth(10);

                //hide the id columns
                $worksheet->getColumnDimension('A')->setVisible(false);
                $worksheet->getColumnDimension('B')->setVisible(false);
                $worksheet->getColumnDimension('E')->setVisible(false);
                $worksheet->getColumnDimension('G')->setVisible(false);
                $worksheet->getColumnDimension('I')->setVisible(false);
                $worksheet->getRowDimension('1')->setVisible(false);
                $worksheet->getRowDimension('2')->setVisible(false);

                foreach ($worksheet->getRowIterator() as $row) {
                    // Find the corresponding row element in the HTML table
                    $rowIndex = $row->getRowIndex();

                    $rowElement = $rows->item($rowIndex - 1); // -1 because row indices start at 1 in PhpSpreadsheet

                    if ($rowElement == null) {
                        continue;
                    }

                    // Get the class name of the row element

                    $className = $rowElement->getAttribute('class');

                    // Check if the class name matches a highlight class from the HTML table
                    if (preg_match('/highlight-(\w+)/', $className, $matches)) {
                        $highlightClass = $matches[1];

                        // Set the fill color based on the highlight class
                        $fillColor = null;
                        switch ($highlightClass) {
                            case 'heading1':
                                $fillColor = ExcelStyles::heading1();
                                break;
                            case 'heading2':
                                $fillColor = ExcelStyles::heading2();
                                break;
                            case 'heading3':
                                $fillColor = ExcelStyles::heading3();
                                break;
                            case 'heading4':
                                $fillColor = ExcelStyles::fill_yellow();
                                break;
                        }

                        if ($fillColor) {
                            $highestColumn = $worksheet->getHighestColumn();
                            $range = 'A' . $rowIndex . ':' . $highestColumn . $rowIndex;
                            $worksheet->getStyle($range)->applyFromArray($fillColor);
                        }
                    }
                }

                $hasEnterprises++; // Set the flag to true if enterprises are found

                $protection = $worksheet->getProtection();
                $protection->setAlgorithm(Protection::ALGORITHM_SHA_512);
                $protection->setPassword('PhpSpreadsheet');
                $protection->setSheet(true);
                $protection->setSort(false);
                $protection->setInsertRows(false);
                $protection->setFormatCells(false);

                $max_rows = $worksheet->getHighestRow();

                //get worksheet name
                $columns = $this->columns[$groups->name];

                $col = 'K';
                foreach ($columns as $key => $column) {
                    $row = 1;
                    while ($row <= $max_rows) {
                        //if a cell is not empty
                        if (is_numeric($worksheet->getCell("A$row")->getValue())) {
                            $worksheet->getStyle($col.$row)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                            
                            $validation = $worksheet->getCell($col.$row)->getDataValidation();
                            $validation->setErrorStyle(DataValidation::STYLE_STOP);
                            $validation->setAllowBlank(true);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setErrorTitle('Input error');
                            $validation->setPromptTitle('Allowed input');
                            $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                            
                            //set datatype of the current cell as number
                            if($column['rules']=='number'){
                                $validation->setType(DataValidation::TYPE_WHOLE);
                                $validation->setError('Only numbers allowed!');
                                $validation->setPrompt('Values greater than or equal to 0 are allowed.');
                            } else if($column['rules']=='decimal'){
                                $validation->setType(DataValidation::TYPE_DECIMAL);
                                $validation->setError('Only numbers/decimals allowed!');
                                $validation->setPrompt('Values greater than or equal to 0 are allowed.');
                            }

                        }
                        $row++;
                    }
                    $col++;
                }
            }

            $sheetindex++;
        }

        //if sheetindex is 0 then there are no enterprises

        if (!$hasEnterprises) {
            return redirect()->to(admin_url('enterprises/transaction'))->with('message', 'No enterprises found.');
        }

        //remove the default worksheet
        $spreadsheet->removeSheetByIndex(
            $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet')
            )
        );


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function upload()
    {
        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]',
                'max_size[file,1024]',
                'ext_in[file,xlsx]',
            ]
        ]);

        if (!$input) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid file',
                'errors' => $this->validator->getErrors()
            ]);
        } else {

            $file = $this->request->getFile('file');

            try {
                $reader = IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($file);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid file.'
                ]);
            }

            //for redirection
            $url = admin_url('enterprises/transaction');
            $status = false;
            $message = '';

            //check existing data
            $activesheet = $spreadsheet->getSheet(0);

            $row_data = $activesheet->toArray();

            $year_id = $row_data[1][0];
            $district_id = $row_data[1][1];
            $month_id = $row_data[1][2];
            $period = $row_data[1][3];

            //check if exists
            $data = [
                'year_id' => $year_id,
                'district_id' => $district_id,
                'month_id' => $month_id,
                'period' => $period,
            ];

            $exists = $this->enterpriseTxnModel->isExists($data);
         
            if ($exists) {
                $status = false;
                $message = 'Enterprise data for the given period already exists.';
            } else {
                $total_sheets = $spreadsheet->getSheetCount();
                // printr($total_sheets);exit;

                for ($i = 0; $i < $total_sheets; $i++) {
                    $activesheet = $spreadsheet->getSheet($i);
                    $sheet_name = $spreadsheet->getSheetNames()[$i];
                    $columns_array = $this->columns[$sheet_name];

                    $row_data = $activesheet->toArray();

                    //skip 3 rows
                    $row_data = array_slice($row_data, 4);

                    $unit_id = [];
                    foreach ($row_data as $key => $column) {
                        //only rows with unit_id
                        if (is_numeric($column[0]))  { //unit_id column is 0
                            $t_data = [
                                'year_id' => (int)$year_id,
                                'district_id' => (int) $district_id,
                                'month_id' => (int)$month_id,
                                'period' => (int)$period,
                            ];

                            if(!in_array($column[0], $unit_id)) { //skip rows with same unit_id
                                $unit_id[] = $column[0];
                                $t_data['unit_id'] = $column[0];
                                $txn_id = $this->enterpriseTxnModel->insert($t_data);

                                //for every unit_id, loop through every rows again
                                foreach ($row_data as $_column) {
                                    if(is_numeric($_column[0]) && ($_column[0] == $column[0])) {
                                        $td_data = [
                                            'enterprise_id' => (int)$_column[1],
                                            'transaction_id' => (int) $txn_id,
                                            'block_id' => (int) $_column[4],
                                            'gp_id' => (int)$_column[6],
                                            'village_id' => (int) $_column[8],
                                        ];
                                        //get column names by unit_groups
                                        $col = 10; //value starts from 10th column

                                        foreach ($columns_array as $key => $value) {
                                            $td_data[$key] = (float)$_column[$col++];
                                        }
                                        $this->enterpriseTxnsDtls->insert($td_data);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $response = [
            'status' => $status,
            'url' => $url,
            'message' => $message
        ];

        $this->session->setFlashdata('message', $message);

        return $this->response->setJSON($response);
    }
}
