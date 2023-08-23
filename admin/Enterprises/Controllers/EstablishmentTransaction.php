<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\EstablishmentTransactionModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\DistrictModel;

use Admin\Enterprises\Models\MonthModel;
use Admin\Enterprises\Models\YearModel;
use App\Controllers\AdminController;
use App\Libraries\Export;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class EstablishmentTransaction extends AdminController
{

    public function index()
    {
        helper('form');
        $this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
        $establishmentransaction = new EstablishmentTransactionModel();
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

        $month_id = $this->request->getGet('month_id');
        $year_id = $this->request->getGet('year_id');
        $district_id = $this->request->getGet('district_id');
        $period = $this->request->getGet('period');
        $data['excel_link'] = admin_url('enterprises/download?district_id=' . $district_id . '&month_id=' . $month_id . '&year_id=' . $year_id . '&period=' . $period);
        $data['upload_url'] = admin_url('enterprises/upload');


        return $this->template->view('Admin\Enterprises\Views\establishmentTransaction', $data);
    }
    public function download()
    {
        $monthmodel = new MonthModel();
        $yearmodel = new YearModel();
        $enterpriseunitsmodel = new EnterprisesUnitModel();
        $establishmentransaction = new EstablishmentTransactionModel();
        $units = $enterpriseunitsmodel->findAll();

        $month_id = $this->request->getGet('month_id');
        $year_id = $this->request->getGet('year_id');
        $district_id = $this->request->getGet('district_id');
        $period = $this->request->getGet('period');

        foreach ($units as $unit) {
            $data['trans'][$unit->id] = [
                'unit_name' => $unit->name,
                'enterprises' => $establishmentransaction->getAll($unit->id, $district_id)
            ];
        }

        $month_name = $monthmodel->find($month_id)->name;
        $year_name = $yearmodel->find($year_id)->name;
        $table = view('Admin\Enterprises\Views\excelForm', $data);
        // dd($table);
        $filename = 'EntTxnTemplate_' . $month_name . '_' . $year_name . '_' . date('Y-m-d_His') . '.xlsx';
        // dd($filename);

        $spreadsheet = Export::createExcelFromHTML($table, $filename, true);
        // dd($spreadsheet);
        if ($spreadsheet) {
            $worksheet = $spreadsheet->getActiveSheet();

            //add period, month_id, year_id

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }

        exit;
    }
    public function upload()
    {
        $establishmentransaction = new EstablishmentTransactionModel();
        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]',
                'max_size[file,1024]',
                'ext_in[file,xlsx]',
            ]
        ]);

        //         if (!$input) {
        //             return $this->response->setJSON([
        //                 'status' => false,
        //                 'message' => 'Invalid file',
        //                 'errors' => $this->validator->getErrors()
        //             ]);
        //         } else {

        //             $file = $this->request->getFile('file');

        //             try {
        //                 $reader = IOFactory::createReader('Xlsx');
        //                 $spreadsheet = $reader->load($file);
        //             } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        //                 return $this->response->setJSON([
        //                     'status' => false,
        //                     'message' => 'Invalid file.'
        //                 ]);
        //             }

        //             $activesheet = $spreadsheet->getSheet(0);

        //             $row_data = $activesheet->toArray();
        // printr($row_data);
        // exit;

        $config['upload_path'] = 'assets/uploads';
        $config['allowed_types'] = 'xls|xlsx';
        $this->load->library('upload', $config);
        $var = 'ob_file';
        $tradatadetails = [];
        $tradata = [];
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($var)) {
            $this->session->set_flashdata('upload_error', $this->upload->display_errors());
            redirect('enterprises/transaction');
        } else {
            $data = $this->upload->data();
            //printr($data);
            //exit;
            if (strpos($data['file_name'], 'EnterpriseTransaction-' . $this->agency->district_id) === false) {
                unlink($data['full_path']);

                $this->session->set_flashdata('error_message', 'Invalid File');
                redirect('enterprises/transaction');
            } else if ($data['file_name']) {
                $file = $data['full_path'];

                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($file);
                $sheetCount = $spreadsheet->getSheetCount();


                for ($i = 0; $i < $sheetCount; $i++) {
                    $sheet = $spreadsheet->getSheet($i);
                    $sheetData = $sheet->toArray(null, true, true, true);
                    //printr($sheetData);
                    //exit;
                    array_shift($sheetData);
                    //array_shift($sheetData);
                    //array_shift($sheetData);
                    //printr($sheetData);
                    //exit;
                    foreach ($sheetData as $sheet) {
                        if ($i == 0) {
                            //echo $sheet['A'];
                            if (is_numeric($sheet['A']) && !is_numeric($sheet['B'])) {
                                $unit_id = $sheet['A'];
                                $tradata[$unit_id] = array(
                                    'unit_id' => $unit_id,
                                    'year_id' => $year_id,
                                    'month_id' => $month_id,
                                    'period' => $period,
                                    'district_id' => $this->agency->district_id,
                                    'date_added' => date('Y-m-d')
                                );
                            }
                            if (is_numeric($sheet['A']) && is_numeric($sheet['B'])) {
                                $enterprise_id = $sheet['A'];
                                $enterprise = $this->$establishmentransaction->get($enterprise_id);
                                $tradata[$unit_id]['details'][] = array(
                                    'enterprise_id' => $enterprise_id,
                                    'block_id' => $enterprise->block_id,
                                    'gp_id' => $enterprise->gp_id,
                                    'village_id' => $enterprise->village_id,
                                    'no_of_days_functional' => $sheet['G'],
                                    'produced' => removeComma($sheet['H']),
                                    'charges_per_qtl' => removeComma($sheet['I']),
                                    'total_expend' => removeComma($sheet['J']),
                                    'total_turnover' => removeComma($sheet['K']),
                                    'date_added' => date('Y-m-d')
                                );
                            }
                        } else if ($i == 1) {
                            //echo $sheet['A'];
                            if (is_numeric($sheet['A']) && !is_numeric($sheet['B'])) {
                                $unit_id = $sheet['A'];
                                $tradata[$unit_id] = array(
                                    'unit_id' => $unit_id,
                                    'year_id' => $year_id,
                                    'month_id' => $month_id,
                                    'period' => $period,
                                    'district_id' => $this->agency->district_id,
                                    'date_added' => date('Y-m-d')
                                );
                            }
                            if (is_numeric($sheet['A']) && is_numeric($sheet['B'])) {

                                $enterprise_id = $sheet['A'];
                                $enterprise = $this->$establishmentransaction->get($enterprise_id);
                                $tradata[$unit_id]['details'][] = array(
                                    'enterprise_id' => $enterprise_id,
                                    'block_id' => $enterprise->block_id,
                                    'gp_id' => $enterprise->gp_id,
                                    'village_id' => $enterprise->village_id,
                                    'no_of_days_functional' => $sheet['G'],
                                    'produced' => '',
                                    'charges_per_qtl' => '',
                                    'total_expend' => removeComma($sheet['H']),
                                    'total_turnover' => removeComma($sheet['I']),
                                    'date_added' => date('Y-m-d')
                                );
                            }
                        }
                    }
                }   
                //printr($tradata);
                //exit;
                foreach ($tradata as $tdata) {
                    $filter = [
                        'district_id' => $tdata['district_id'],
                        'year_id' => $tdata['year_id'],
                        'month_id' => $tdata['month_id'],
                        'period' => $tdata['period'],
                        'unit_id' => $tdata['unit_id'],
                    ];
                    $transaction = $this->$establishmentransaction->getCheckEnterpriseTransaction($filter);
                    //printr($transaction);
                    if ($transaction) {
                        $this->$establishmentransaction->editTransaction($transaction['id'], $tdata);
                    } else {
                        $this->$establishmentransaction->addTransaction($tdata);
                    }
                }
                $this->session->set_flashdata('message', 'Upload successfully');
                redirect('enterprises/transaction');
                /*$this->session->set_userdata('ob_file', $data);
                $this->session->set_userdata('year_id', $year_id);
                $this->session->set_userdata('month_id', $month_id);*/
            }
        }
    }
}
