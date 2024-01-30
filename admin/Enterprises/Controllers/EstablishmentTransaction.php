<?php

namespace Admin\Enterprises\Controllers;

use Admin\Dashboard\Models\GpsModel;
use Admin\Dashboard\Models\BlockModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Admin\Enterprises\Models\EstablishmentTransactionModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Dashboard\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesTransactionModel;
use Admin\Enterprises\Models\EstablishmentTransactionDetailsModel;
use Admin\Enterprises\Models\MonthModel;
use Admin\Enterprises\Models\EnterpriseVillagesModel;
use Admin\Dashboard\Models\YearModel;
use Admin\Enterprises\Models\EnterpriseUnitGroup;
use Admin\Localisation\Controllers\Block;
use Admin\Localisation\Controllers\District;
use App\Controllers\AdminController;
use App\Libraries\Export;
use Config\ExcelStyles;
use Mpdf\Tag\Tr;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class EstablishmentTransaction extends AdminController
{
    private $columns = [
        'Machinary' => [
            'no_of_days_functional' => 'No. of Days Functional',
            'total_turnover' => 'Total turnover / sale value',
            'produced' => 'Quintals of Produce processed',
            'total_expend' => 'Total expenditure',
            'under_maintenance' => 'No. of times under maintenance',
        ],
        'Food' => [
            'no_of_days_functional' => 'No. of Days Functional',
            'total_turnover' => 'Total turnover / sale value',
            'total_expend' => 'Total expenditure',
            'event_attend' => 'No. of event attend',
        ],
        'CHC' => [
            'farmer_user' => 'NO. of farmer user',
            'service_charge' => 'Value of service charge collected (in rupees)',
            'total_expend' => 'Expenditure if any(rupees)',
        ],
        'CMSC' => [
            'farmer_user' => 'NO. of farmer user',
            'seed_support' => 'Quantity of seed supported (in quintals)',
            'seed_store' => 'Quantity of seed in store (in quintals)',
            'service_charge' => 'Value of service charges & sale of seed',
            'total_expend' => 'Expenditure if any (in rupees)',
        ],
    ];

    public function index()
    {
        helper('form');
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
        $establishmentransaction = new EstablishmentTransactionModel();
        $establishmenttrasdtl = new EstablishmentTransactionDetailsModel();
        $yearModel = new YearModel();
        $data['years'][0] = 'Select Year';

        $data['year_id'] = 0;
        $years = $yearModel->findAll();

        foreach ($years as $year) {
            $data['years'][$year->id] = $year->name;
        }

        $data['year_id'] = $yearModel->getCurrentYear()->id;;
        if ($this->request->getGet('year_id')) {
            $data['year_id'] = $this->request->getGet('year_id');
        }

        $monthmodel = new MonthModel();
        $data['months'][0] = 'Select Month';

        $months = $monthmodel->findAll();

        foreach ($months as $month) {
            $data['months'][$month->id] = $month->name;
        }

        $data['month_id'] = 0;
        if ($this->request->getGet('month_id')) {
            $data['month_id'] = $this->request->getGet('month_id');
        }
        $districtmodel = new DistrictModel();
        $data['districts'][0] = 'Select Districts';

        if ($this->user->district_id) {
            $districts = $districtmodel->where('id', $this->user->district_id)->orderBy('name', 'asc')->findAll();
        } else {
            $districts = $districtmodel->orderBy('name', 'asc')->findAll();
        }

        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $data['district_id'] = $this->user->district_id;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
        }
        //unit
        $entunitmodel = new EnterprisesUnitModel();
        $data['units'][0] = 'Select units';

        $units = $entunitmodel->orderBy('name', 'asc')->findAll();

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
        $filter = [];

        if ($data['year_id'] > 0) {
            $filter['year_id'] = $data['year_id'];
        }

        if ($data['district_id'] > 0) {
            $filter['district_id'] = $data['district_id'];
        }

        if ($data['month_id'] > 0) {
            $filter['month_id'] = $data['month_id'];
        }
        if ($data['unit_id'] > 0) {
            $filter['unit_id'] = $data['unit_id'];
        }
        if ($data['period'] > 0) {
            $filter['period'] = $data['period'];
        }
        $ent_trans = $establishmenttrasdtl->periodswisetrans($filter);
        $data['trans'] = [];

        foreach ($ent_trans as $row) {
            $data['trans'][] = [
                'units' => $row->unit_name,
                'period' => $row->period,
                'month' => $row->month_name,
                'districts' => $row->district_name,
                'blocks' => $row->block_name,
                'gp' => $row->gp_name,
                'villages' => $row->village_name,
                'years' => $row->year_name,
                'created_at' => ymdToDmy($row->created_at),
                'edit_url' => admin_url('enterprisestrans/edit?id=' . $row->est_id),

            ];
        }
        $data['excel_link'] = admin_url('enterprisestrans/download');
        $data['upload_url'] = admin_url('enterprisestrans/upload');

    

        return $this->template->view('Admin\Enterprises\Views\establishmentTransaction', $data);
    }
    public function edit()
    {
        $establishmenttrasdtl = new EstablishmentTransactionDetailsModel();

        if ($this->request->getMethod(1) == 'POST') {
            $id = $this->request->getGet('id');
            $enterprisetransdata = [];
            $entdata = $establishmenttrasdtl->idwisetrans($id);

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

            $establishmenttrasdtl->update($id, $enterprisetransdata);

            return redirect()->to(admin_url('enterprises/transaction'))->with('message', 'update successful');
        }


        return $this->getForm();
    }
    private function getForm()
    {
        helper('form');
        $establishmenttrasdtl = new EstablishmentTransactionDetailsModel();
        $data['unit_groups'] = $this->columns;
        $id = $this->request->getGet('id');
        if ($id) {
            $entdata = $establishmenttrasdtl->idwisetrans($id);
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
            return $this->template->view('Admin\Enterprises\Views\editEstablishmentTransaction', $data);
        }

    }



    public function download()
    {

        $enterprisesmodel = new EnterprisesModel();
        $enterprisestransaction = new EnterprisesTransactionModel();
        $monthmodel = new MonthModel();
        $yearmodel = new YearModel();
        $enterpriseunitsmodel = new EnterprisesUnitModel();
        $establishmentransaction = new EstablishmentTransactionModel();
        $ugModel = new EnterpriseUnitGroup();
        $unit_groups = $ugModel->findAll();
        $worksheet_unit = [];
        foreach ($unit_groups as $group) {
            $units = $enterpriseunitsmodel->getAll(['unit_group_id' => $group->id]);
            $group->units = $units;
        }
        $month_id = $this->request->getGet('month_id');
        $year_id = $this->request->getGet('year_id');
        $district_id = $this->request->getGet('district_id');
        $period = $this->request->getGet('period');
        $month_name = $monthmodel->find($month_id)->name;
        $year_name = $yearmodel->find($year_id)->name;
        $filename = 'EntTxnTemplate_' . $month_name . '_' . $year_name . '_' . date('Y-m-d_His') . '.xlsx';
        $sheetindex = 0;
        $reader = new Html();
        $doc = new \DOMDocument();
        $spreadsheet = new Spreadsheet();
        $data = [
            'month_id' => $month_id,
            'year_id' => $year_id,
            'district_id' => $district_id,
            'period' => $period
        ];

        //loop through all unit groups
        foreach ($unit_groups as $group) {

            // fetch enterprises by district_id and unit_id
            foreach ($group->units as &$unit) {
                $enterprises = $enterprisesmodel->getBy([
                    'district_id' => $district_id,
                    'unit_id' => $unit['id']
                ]);
                $unit['enterprises'] = $enterprises;
                $group->columns = [];

                //if enterprises are available then add columns
                if ($enterprises) {
                    $group->columns = $this->columns[$unit['unit_group']];

                    $data['columns'] = $this->columns[$unit['unit_group']];

                    // heading title
                    $title = $unit['unit_group'];
                    $data['heading_title'] = "Enterprise Transaction " . ucfirst($title) . " Data";

                    $data['enterprises'] = $enterprises;

                    $htmltable = view('Admin\Enterprises\Views\excelForm', $data);

                    $htmltable = preg_replace("/&(?!\S+;)/", "&amp;", $htmltable);

                    $worksheet = $spreadsheet->createSheet($sheetindex);

                    $worksheet->setTitle($title);

                    $reader->setSheetIndex($sheetindex);
                    $spreadsheet = $reader->loadFromString($htmltable, $spreadsheet);

                    $worksheet = $spreadsheet->getActiveSheet();

                    // Load HTML content into a DOM object for formatting from class
                    $doc->loadHTML($htmltable);

                    $rows = $doc->getElementsByTagName('tr');

                    //formatting and designing
                    foreach ($worksheet->getRowIterator() as $row) {
                        // Find the corresponding row element in the HTML table
                        $rowIndex = $row->getRowIndex();

                        $rowElement = $rows->item($rowIndex - 1); // -1 because row indices start at 1 in PhpSpreadsheet

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
                                $lastColumnIndex = $worksheet->getHighestColumn();
                                $range = 'A' . $rowIndex . ':' . $lastColumnIndex . $rowIndex;
                                $worksheet->getStyle($range)->applyFromArray($fillColor);
                            }
                        }

                        // Set auto-size column widths for all columns
                        $colsToWrap = ['D', 'F', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];

                        $highestRow = $worksheet->getHighestRow();

                        foreach ($colsToWrap as $colToWrap) {
                            $range = $colToWrap . '1:' . $colToWrap . $highestRow;

                            $style = $worksheet->getStyle($range);
                            $style->getAlignment()->setWrapText(true);
                        }

                        //hide the id columns
                        if ($worksheet) {
                            $worksheet->getColumnDimension('A')->setVisible(false);
                            $worksheet->getColumnDimension('B')->setVisible(false);
                            $worksheet->getColumnDimension('E')->setVisible(false);
                            $worksheet->getColumnDimension('G')->setVisible(false);
                            $worksheet->getColumnDimension('I')->setVisible(false);
                            $worksheet->getRowDimension('1')->setVisible(false);
                            $worksheet->getRowDimension('2')->setVisible(false);
                        }
                    }
                    $sheetindex++;
                }
            }
        }

        //remove the default worksheet
        $spreadsheet->removeSheetByIndex(
            $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet')
            )
        );

        //start protection
        //
        $spreadsheet->setActiveSheetIndex(0);

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
        $enterpriseunitsmodel = new EnterprisesUnitModel();
        $enterprisestransaction = new EnterprisesTransactionModel();
        $enterprisetransdetails = new EstablishmentTransactionDetailsModel();
        $establishmentransaction = new EstablishmentTransactionModel();
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

            $exists = $enterprisestransaction->isExists($data);

            if ($exists) {
                $status = false;
                $message = 'Enterprise data for the given period already exists.';
            } else {
                $total_sheets = $spreadsheet->getSheetCount();

                for ($i = 0; $i < $total_sheets; $i++) {
                    $activesheet = $spreadsheet->getSheet($i);

                    $row_data = $activesheet->toArray();

                    //skip 3 rows
                    $row_data = array_slice($row_data, 3);

                    foreach ($row_data as $key => $transaction) {
                        //only rows with gp_id
                        if (is_numeric($transaction[1])) {
                            $data = [
                                'unit_id' => (int)$transaction[0],
                                'year_id' => (int)$year_id,
                                'district_id' => (int) $district_id,
                                'month_id' => (int)$month_id,
                                'period' => (int)$period,
                            ];

                            $transaction_id = $enterprisestransaction->insert($data);

                            $data = [
                                'enterprise_id' => (int)$transaction[1],
                                'transaction_id' => (int) $transaction_id,
                                'block_id' => (int) $transaction[4],
                                'gp_id' => (int)$transaction[6],
                                'village_id' => (int) $transaction[8],
                            ];

                            //get columns by sheet name
                            $col = 10;
                            $columns = $this->columns[$spreadsheet->getSheetNames()[$i]];
                            foreach ($columns as $key => $value) {
                                $data[$key] = (float)$transaction[$col++];
                            }

                            $enterprisetransdetails->insert($data);
                            $status = true;
                            $message = 'Uploaded successfully';
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
