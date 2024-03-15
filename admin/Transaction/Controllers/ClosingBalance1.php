<?php

namespace Admin\Transaction\Controllers;

use App\Controllers\AdminController;
use Config\Url;
use Admin\Common\Models\AllowuploadModel;
use Admin\Common\Models\MonthModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Transaction\Models\ClosingbalanceModel;
use Admin\Users\Models\UserGroupModel;
use Admin\Users\Models\UserModel;

class ClosingBalance extends AdminController
{
    private $cbModel;
    private $monthModel;
    private $uploadModel;

    public function __construct()
    {
        $this->cbModel = new ClosingbalanceModel();
        $this->monthModel = new MonthModel();
        $this->uploadModel = new AllowuploadModel();
    }

    public function index()
    {
        $filter = $this->getFilter();

        $data['months'] = $this->cbModel->getLedgerReport($filter);

        $data['year_id'] = $filter['year'];
        $data['agency_type_id'] = $filter['agency_type_id'];

        $data['message'] = $this->session->getFlashdata('message');

        $data['agency_types'] = [];
        if ($this->user->agency_type_id == $this->settings->block_user) {
            $data['agency_types'] = (new UserGroupModel)->getBlockUsers();
        }

        $data['fund_agencies'] = [];
        if ($this->user->agency_type_id != $this->settings->block_user) {
            if ($this->user->district_id) {
                $data['fund_agencies'] = (new BlockModel())->getFundAgencies(['district_id' => $this->user->district_id]);
            } else {
                $data['fund_agencies'] = (new BlockModel())->getFundAgencies();
            }
        }

        return $this->template->view('Admin\Transaction\Views\closingbalance', $data);
    }

    public function info($year, $month, $agency_type_id, $fund_agency_id)
    {
        $this->template->add_package(['uploader', 'jquery_loading'], true);
        helper('form');

        $filter = $this->getFilter();
        $filter['month'] = $month;
        $cb = $this->cbModel->where($filter)->first();

        $data['can_edit'] = false;
        $uploads = $this->uploadModel->getByDate(['user_id' => $this->user->user_id]);
        $months = [];
        foreach ($uploads as $item) {
            $months[] = $item['month'];
        }
        $data['can_edit'] = in_array($month, $months);

        if ($this->request->getMethod() == 'post') {
            $this->validate([
                'advance' => 'required|numeric',
                'bank' => 'required|numeric',
                'cash' => 'required|numeric',
            ]);

            if (!$this->validator->getErrors()) {
                if ($cb) {
                    $this->cbModel->delete($cb->id);
                }

                $data = [
                    'user_id' => $this->user->getId(),
                    'month' => $month,
                    'year' => $year,
                    'block_id' => $this->user->block_id,
                    'district_id' => $this->user->district_id,
                    'agency_type_id' => $agency_type_id,
                    'fund_agency_id' => $fund_agency_id,
                    'status' => (int)in_array($this->user->agency_type_id, $this->settings->auto_approve_users)
                ];

                $data['advance'] = (float)$this->request->getPost('advance');
                $data['bank'] = (float)$this->request->getPost('bank');
                $data['cash'] = (float)$this->request->getPost('cash');

                $this->cbModel->save($data);

                return redirect()->to(Url::closingBalance)->with('message', 'Closing balance submitted for approval');
            }
        }

        $filter['fund_agency_id'] = $fund_agency_id;
        $ledger = $this->cbModel->getLedgerReport($filter, 'array');

        $key = array_search($month, array_column($ledger, 'month_id'));

        $data['summary'] = $ledger[$key];
        if ($cb) {
            $data['summary']['status'] = $this->statuses[$cb->status];
            $data['can_edit'] = $cb->status == 0 || $cb->status == 2;
        } else {
            $data['summary']['status'] = $this->statuses[3];
        }

        return $this->template->view('Admin\Transaction\Views\closingbalance_form', $data);
    }

    public function upload()
    {
        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/png,image/JPG,image/JPEG,image/PNG,application/pdf,application/PDF,application/x-pdf]',
                'max_size[file,5
