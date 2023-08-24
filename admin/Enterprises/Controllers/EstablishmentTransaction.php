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

            $activesheet = $spreadsheet->getSheet(0);

            $row_data = $activesheet->toArray();
        }
        foreach ($row_data as $transaction) {
            //only rows with gp_id
            if (is_numeric($transaction[0])) {
                $transaction_data = [
                    'managing_unit_name' => $transaction[0],
                    'unit_name' => $transaction[1],
                    'block' => $transaction[2],
                    'grampanchayat' => $transaction[3],
                    'season' => $transaction[4],
                    'villages' => $transaction[5],
                   
                ];

                //                        $ac_crop_coverage_id = 0;
                $transactions = $establishmentransaction->insert($transaction_data);
                dd($transactions);
            }
        }
    }
}
