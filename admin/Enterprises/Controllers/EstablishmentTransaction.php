<?php

namespace Admin\Enterprises\Controllers;

use Admin\Dashboard\Models\GpsModel;
use Admin\Enterprises\Models\BlockModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Admin\Enterprises\Models\EstablishmentTransactionModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesTransactionModel;
use Admin\Enterprises\Models\EstablishmentTransactionDetailsModel;
use Admin\Enterprises\Models\MonthModel;
use Admin\Enterprises\Models\VillagesModel;
use Admin\Enterprises\Models\YearModel;
use Admin\Localisation\Controllers\Block;
use Admin\Localisation\Controllers\District;
use App\Controllers\AdminController;
use App\Libraries\Export;
use Config\ExcelStyles;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class EstablishmentTransaction extends AdminController
{
    public function index()
    {
        helper('form');
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
        $establishmentransaction = new EstablishmentTransactionModel();
        $establishmenttrasdtl = new EstablishmentTransactionDetailsModel();
        $yearmodel = new YearModel();
        $data['years'][0] = 'Select Year';

        $data['year_id'] = 0;
        $years = $yearmodel->findAll();

        foreach ($years as $year) {
            $data['years'][$year->id] = $year->name;
        }

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

        $districts = $districtmodel->findAll();

        foreach ($districts as $district) {
            $data['districts'][$district->id] = $district->name;
        }

        $data['district_id'] = 0;
        if ($this->request->getGet('district_id')) {
            $data['district_id'] = $this->request->getGet('district_id');
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
        if ($data['period'] > 0) {
            $filter['period'] = $data['period'];
        }
        $ent_trans = $establishmenttrasdtl->periodswisetrans($filter);
        // dd($ent_trans);
        $data['trans'] = [];

        foreach ($ent_trans as $row) {
            $data['trans'][] = [
                // 'created_at' => $row->created_at,
                'units' => $row->unit_name,
                'period' => $row->period,
                'month' => $row->month_name,
                'districts' => $row->district_name,
                'blocks' => $row->block_name,
                'gp' => $row->gp_name,
                'villages' => $row->village_name,
                'years' => $row->year_name,
                'created_at' => ymdToDmy($row->created_at),
                'edit_url' => admin_url('enterprisestrans/edit?id=' . $row->id),

            ];
            // printr($data);exit;
        }
        $data['excel_link'] = admin_url('enterprises/download');
        $data['upload_url'] = admin_url('enterprises/upload');

       // dd($data['excel_link']);

        return $this->template->view('Admin\Enterprises\Views\establishmentTransaction', $data);
    }
    public function edit()
    {
        $establishmenttrasdtl = new EstablishmentTransactionDetailsModel();
        if ($this->request->getMethod(1) == 'POST') {
            $id = $this->request->getGet('id');

            $enterprisetransdata = [
                'no_of_days_functional' => $this->request->getPost('no_of_days_functional'),
                'produced' => $this->request->getPost('produced'),
                'charges_per_qtl' => $this->request->getPost('charges_per_qtl'),
                'total_expend' => $this->request->getPost('total_expend'),
                'total_turnover' => $this->request->getPost('total_turnover'),
                'created_at' => $this->request->getPost('created_at'),


            ];
            // dd($id);
            // printr($enterprisetransdata);
            // exit;

            $establishmenttrasdtl->update($id, $enterprisetransdata);
            return redirect()->to(admin_url('enterprises/transaction'))->with('message', 'successful');
        }


        return $this->getForm();
    }
    private function getForm()
    {
        helper('form');
        $establishmenttrasdtl = new EstablishmentTransactionDetailsModel();
        $id = $this->request->getGet('id');
        // dd($id);
        if ($id) {

            $entdata = $establishmenttrasdtl->periodswisetrans(['id' => $id]);
            // printr($entdata);exit;
            foreach ($entdata as $value) {
              
                $data['entranses'] = [
                    'year_id' => $value->year_name,
                    'unit_id' => $value->unit_name,
                    'district_id' => $value->district_name,
                    'block_id' => $value->block_name,
                    'month_id' => $value->month_name,
                    'gp_id' => $value->gp_name,
                    'village_id' => $value->village_name,
                    'period' => $value->period,
                    'created_at' => ymdToDmy($value->created_at) ?: 0,
                    'no_of_days_functional' => $value->no_of_days_functional ?: 0,
                    'produced' => $value->produced ?: 0,
                    'charges_per_qtl' => $value->charges_per_qtl ?: 0,
                    'total_expend' => $value->total_expend ?: 0,
                    'total_turnover' => $value->total_turnover ?: 0,

                ];
            }
        }

// dd($data['entranses']);
        return $this->template->view('Admin\Enterprises\Views\editEstablishmentTransaction', $data);
    }

    public function download()
    {

        //  printr($this->request->getGet()); exit;
        $enterprisesmodel = new EnterprisesModel();
        $enterprisestransaction = new EnterprisesTransactionModel();
        $monthmodel = new MonthModel();
        $yearmodel = new YearModel();
        $enterpriseunitsmodel = new EnterprisesUnitModel();
        $establishmentransaction = new EstablishmentTransactionModel();
        $units = $enterpriseunitsmodel->findAll();

        $district_id = $this->request->getGet('district_id');
        //  dd($units);

        $worksheet_unit = [];
        foreach ($units as $key => $unit) {

            $filter = [
                'id' => $unit->id,
                'district_id' => $district_id
            ];
            $details = $enterpriseunitsmodel->getAll($filter);
            // dd($details);
            $worksheet_unit[$key]['id'] = $unit->id;
            $worksheet_unit[$key]['name'] = $unit->name;
            $worksheet_unit[$key]['group_unit'] = $unit->group_unit;
            $worksheet_unit[$key]['details'] = $details;
        }
        $worksheet_unit = $this->_group_by($worksheet_unit, 'group_unit');

        $month_id = $this->request->getGet('month_id');
        $year_id = $this->request->getGet('year_id');
        $district_id = $this->request->getGet('district_id');
        $period = $this->request->getGet('period');

        $month_name = $monthmodel->find($month_id)->name;
        $year_name = $yearmodel->find($year_id)->name;
        $filename = 'EntTxnTemplate_' . $month_name . '_' . $year_name . '_' . date('Y-m-d_His') . '.xlsx';
        //dd($worksheet_unit);

        // $spreadsheetMain = new Spreadsheet();

        // $spreadsheetMain->removeSheetByIndex(
        //     $spreadsheetMain->getIndex(
        //         $spreadsheetMain->getSheetByName('Worksheet')
        //     )
        // );

        $sheetindex = 0;
        $reader = new Html();
        $doc = new \DOMDocument();
        $spreadsheet = new Spreadsheet();

        foreach ($worksheet_unit as $key => $units) {
            if ($key == "p") {
                $title = "Machinaries";
                $end = "K";
            } else {
                $title = "Food Unit";
                $end = "I";
            }
            $heading = "Enterprise Transaction $title Data";

            $data = [
                'month_id' => $month_id,
                'year_id' => $year_id,
                'district_id' => $district_id,
                'period' => $period
            ];

            foreach ($units as $unit) {
                $data['trans'][$unit['id']] = [
                    'unit_name' => $unit['name'],
                    'id' => $unit['id'],
                    'enterprises' => $establishmentransaction->getAll($unit['id'], $district_id)
                ];
            }
            $data['heading_title'] = $heading;

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
            }

            // Set auto-size column widths for all columns
            $colsToWrap = ['D', 'F', 'H', 'J', 'K', 'L', 'M', 'N', 'O'];

            $highestRow = $worksheet->getHighestRow();

            foreach ($colsToWrap as $colToWrap) {
                $range = $colToWrap . '1:' . $colToWrap . $highestRow;

                $style = $worksheet->getStyle($range);
                $style->getAlignment()->setWrapText(true);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('output_file.xlsx');
            if ($worksheet) {
                $worksheet->getColumnDimension('A')->setVisible(false);
                $worksheet->getColumnDimension('B')->setVisible(false);
                $worksheet->getColumnDimension('E')->setVisible(false);
                $worksheet->getColumnDimension('G')->setVisible(false);
                $worksheet->getColumnDimension('I')->setVisible(false);
                $worksheet->getRowDimension('1')->setVisible(false);
                $worksheet->getRowDimension('2')->setVisible(false);

                if ($title == "Food Unit") {
                    $worksheet->getColumnDimension('M')->setVisible(false);
                }
            }


            $sheetindex++;
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
            $total_sheets = $spreadsheet->getSheetCount();

            $transaction_id = 0;

            for ($i = 0; $i < $total_sheets; $i++) {
                $activesheet = $spreadsheet->getSheet($i);

                $row_data = $activesheet->toArray();

                $year_id = $row_data[1][0];
                $district_id = $row_data[1][1];
                $month_id = $row_data[1][2];
                $period = $row_data[1][3];

                //skip 3 rows
                $row_data = array_slice($row_data, 3);

                foreach ($row_data as $key => $transaction) {
                    //only rows with gp_id
                    if (is_numeric($transaction[0])) {
                        $transaction_data = [
                            'unit_id' => $transaction[0],
                            'year_id' => $year_id,
                            'district_id' => $district_id,
                            'month_id' => $month_id,
                            'period' => $period,
                        ];

                        $transaction_id = $enterprisestransaction->insert($transaction_data);
                    } else if (is_numeric($transaction[1])) {
                        $transaction_data = [
                            'enterprise_id' => $transaction[1],
                            'transaction_id' => $transaction_id,
                            'block_id' => $transaction[4],
                            'gp_id' => $transaction[6],
                            'village_id' => $transaction[8],
                            'no_of_days_functional' => $transaction[10],
                            'produced' => $transaction[11],
                            'charges_per_qtl' => $transaction[12],
                            'total_expend' => $transaction[13],
                            'total_turnover' => $transaction[14],

                        ];

                        $enterprisetransdetails->insert($transaction_data);
                        $status = true;
                        $message = 'Uploaded successfully';
                    }
                }
            }
        }

        $response = [
            'status' => $status,
            'url' => $url,
            'message' => $message
        ];

        return $this->response->setJSON($response);
    }

    protected function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }
}
